<?php

declare(strict_types=1);

namespace App\Services\Shipping;

use App\Services\CurrencyService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ShippingCalculationService
{
    /**
     * Daftar layanan kurir yang didukung butik PUTRIJALVA.
     */
    public function getAvailableServices(?string $province = null, ?string $city = null): array
    {
        $isJabodetabek = $this->isJabodetabek($province, $city);

        $services = [
            [
                'code' => 'reguler',
                'name' => 'Reguler (JNE / SiCepat / Anteraja)',
                'eta' => '2 - 3 Hari Kerja',
                'base_cost_usd' => 0.0,
                'available' => true,
            ],
            [
                'code' => 'express',
                'name' => 'Express Next Day (JNE YES / SiCepat BEST)',
                'eta' => '1 Hari Kerja',
                'base_cost_usd' => 2.0,
                'available' => true,
            ],
            [
                'code' => 'sameday',
                'name' => 'Sameday Delivery (Grab / Gojek)',
                'eta' => '6 - 8 Jam (Order sebelum 14:00)',
                'base_cost_usd' => 3.5,
                'available' => $isJabodetabek,
            ],
            [
                'code' => 'instant',
                'name' => 'Instant Courier (GrabExpress / GoSend)',
                'eta' => '1 - 2 Jam Tiba',
                'base_cost_usd' => 5.0,
                'available' => $isJabodetabek,
            ],
        ];

        return array_values(array_filter($services, fn ($s) => $s['available']));
    }

    /**
     * Hitung tarif ongkos kirim berdasarkan layanan, lokasi, dan subtotal pesanan.
     */
    public function calculate(string $service, ?string $province = null, ?string $city = null, float $subtotalUsd = 0.0, float $weightKg = 1.0): array
    {
        $service = strtolower(trim($service));
        $isJabodetabek = $this->isJabodetabek($province, $city);

        $costs = [
            'reguler' => 0.0,
            'express' => 2.0,
            'sameday' => 3.5,
            'instant' => 5.0,
        ];

        $names = [
            'reguler' => 'Reguler (JNE / SiCepat / Anteraja)',
            'express' => 'Express Next Day (JNE YES / SiCepat BEST)',
            'sameday' => 'Sameday Delivery (Grab / Gojek)',
            'instant' => 'Instant Courier (GrabExpress / GoSend)',
        ];

        $etas = [
            'reguler' => '2 - 3 Hari Kerja',
            'express' => '1 Hari Kerja',
            'sameday' => '6 - 8 Jam',
            'instant' => '1 - 2 Jam Tiba',
        ];

        if (! array_key_exists($service, $costs)) {
            $service = 'reguler';
        }

        // Jika Sameday/Instant dipilih di luar Jabodetabek, fallback aman ke Express
        if (in_array($service, ['sameday', 'instant'], true) && ! $isJabodetabek && ! empty($province)) {
            $service = 'express';
        }

        $baseCost = $costs[$service];

        // Free Shipping Promotion: Belanja >= threshold gratis ongkir reguler
        $thresholdIdr = (float) config('services.shipping.free_shipping_threshold_idr', 500000.0);
        $rate = app(CurrencyService::class)->getUsdToIdrRate();
        $thresholdUsd = $thresholdIdr / max(1.0, $rate);

        $isFree = false;
        if ($service === 'reguler') {
            $baseCost = 0.0;
            $isFree = true;
        }

        // Optional RajaOngkir Live Adapter
        $rajaApiKey = config('services.shipping.rajaongkir_api_key');
        if (! empty($rajaApiKey) && in_array($service, ['reguler', 'express'], true)) {
            $liveCost = $this->queryRajaOngkir($rajaApiKey, $city, (int) ($weightKg * 1000), $service);
            if ($liveCost !== null) {
                $baseCost = $liveCost / max(1.0, $rate);
            }
        }

        return [
            'service' => $service,
            'name' => $names[$service],
            'eta' => $etas[$service],
            'cost_usd' => $baseCost,
            'cost_formatted' => $isFree ? __('GRATIS') : CurrencyService::formatPrice($baseCost),
            'is_free' => $isFree,
            'weight_kg' => $weightKg,
            'is_jabodetabek' => $isJabodetabek,
        ];
    }

    /**
     * Cek apakah destinasi termasuk wilayah Jabodetabek.
     */
    public function isJabodetabek(?string $province, ?string $city): bool
    {
        $haystack = strtolower(trim(($province ?? '').' '.($city ?? '')));

        if (empty($haystack)) {
            return true; // Default Jabodetabek jika belum diisi
        }

        $keywords = ['jakarta', 'bogor', 'depok', 'tangerang', 'bekasi', 'dki', 'jabodetabek', 'banten', 'jawa barat'];
        foreach ($keywords as $kw) {
            if (str_contains($haystack, $kw)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Query API RajaOngkir jika API key tersedia.
     */
    protected function queryRajaOngkir(string $apiKey, ?string $destinationCity, int $weightGrams, string $service): ?float
    {
        try {
            $origin = config('services.shipping.origin_city_id', '152'); // Jakarta Pusat
            $response = Http::timeout(5)->withHeaders([
                'key' => $apiKey,
            ])->post('https://api.rajaongkir.com/starter/cost', [
                'origin' => $origin,
                'destination' => $destinationCity ?? '152',
                'weight' => max(1000, $weightGrams),
                'courier' => 'jne',
            ]);

            if ($response->successful()) {
                $results = $response->json('rajaongkir.results.0.costs', []);
                foreach ($results as $item) {
                    if ($service === 'express' && ($item['service'] ?? '') === 'YES') {
                        return (float) ($item['cost'][0]['value'] ?? 30000);
                    }
                    if ($service === 'reguler' && ($item['service'] ?? '') === 'REG') {
                        return (float) ($item['cost'][0]['value'] ?? 15000);
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::info('RajaOngkir API fallback: '.$e->getMessage());
        }

        return null;
    }
}
