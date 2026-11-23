<?php

declare(strict_types=1);

namespace App\Services\Shipping;

use App\Services\CurrencyService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ShippingCalculationService
{
    /**
     * Pemetaan nama kota ke ID kota resmi RajaOngkir (Starter/Pro).
     */
    public const RAJAONGKIR_CITY_MAP = [
        'jakarta' => 152,
        'jakarta pusat' => 152,
        'jakarta barat' => 151,
        'jakarta selatan' => 153,
        'jakarta timur' => 154,
        'jakarta utara' => 155,
        'bogor' => 78,
        'depok' => 115,
        'tangerang' => 455,
        'tangerang selatan' => 456,
        'bekasi' => 54,
        'serang' => 402,
        'cilegon' => 106,
        'bandung' => 23,
        'cimahi' => 107,
        'cirebon' => 108,
        'sukabumi' => 427,
        'tasikmalaya' => 468,
        'semarang' => 399,
        'surakarta' => 419,
        'solo' => 419,
        'magelang' => 249,
        'pekalongan' => 345,
        'tegal' => 472,
        'yogyakarta' => 501,
        'jogja' => 501,
        'sleman' => 418,
        'bantul' => 39,
        'surabaya' => 444,
        'malang' => 255,
        'sidoarjo' => 409,
        'gresik' => 133,
        'kediri' => 178,
        'blitar' => 74,
        'madiun' => 248,
        'jember' => 164,
        'banyuwangi' => 41,
        'denpasar' => 114,
        'badung' => 17,
        'mataram' => 274,
        'kupang' => 213,
        'medan' => 278,
        'padang' => 318,
        'palembang' => 327,
        'pekanbaru' => 349,
        'batam' => 48,
        'tanjung pinang' => 460,
        'bandar lampung' => 21,
        'lampung' => 21,
        'jambi' => 156,
        'bengkulu' => 62,
        'pangkal pinang' => 334,
        'banda aceh' => 20,
        'pontianak' => 365,
        'banjarmasin' => 36,
        'banjarbaru' => 35,
        'balikpapan' => 22,
        'samarinda' => 387,
        'palangka raya' => 326,
        'tarakan' => 465,
        'makassar' => 254,
        'manado' => 268,
        'palu' => 328,
        'kendari' => 184,
        'gorontalo' => 131,
        'mamuju' => 258,
        'ambon' => 13,
        'jayapura' => 163,
        'sorong' => 422,
        'merauke' => 279,
    ];

    /**
     * Dapatkan semua opsi layanan pengiriman yang valid untuk destinasi tertentu.
     */
    public function getAvailableServices(?string $province, ?string $city, float $subtotalUsd = 0.0, float $weightKg = 1.0): array
    {
        $zone = $this->determineZone($province, $city);
        $isJabodetabek = ($zone === 'jabodetabek');

        // Bobot dalam kg (pembulatan ke atas min 1 kg)
        $billableKg = max(1.0, ceil($weightKg));
        $rateUsd = app(CurrencyService::class)->getUsdToIdrRate();

        // Cek promo gratis ongkir reguler (default Rp 500.000)
        $thresholdIdr = (float) config('services.shipping.free_shipping_threshold_idr', 500000.0);
        $subtotalIdr = $subtotalUsd * $rateUsd;
        $isEligibleFreeShipping = ($subtotalIdr >= $thresholdIdr);

        // Tarif dasar per zona logistik Indonesia (dalam IDR)
        $zoneRates = $this->getZoneRates($zone, (int) $billableKg);

        // 1. Layanan Reguler (JNE REG / SiCepat REG / J&T EZ)
        $regulerIdr = $zoneRates['reguler'];
        $isRegulerFree = $isEligibleFreeShipping;
        if ($isRegulerFree) {
            $regulerCostUsd = 0.0;
        } else {
            $regulerCostUsd = round($regulerIdr / max(1.0, $rateUsd), 2);
        }

        $services = [];

        // Opsi 1: Reguler
        $services[] = [
            'service' => 'reguler',
            'courier' => 'JNE / SiCepat / J&T',
            'name' => 'Reguler (JNE REG / SiCepat REG / J&T EZ)',
            'description' => 'Pengiriman standar ke seluruh alamat Indonesia dengan nomor resi otomatis.',
            'eta' => $this->getEtaForZone('reguler', $zone),
            'cost_idr' => $isRegulerFree ? 0 : $regulerIdr,
            'cost_usd' => $regulerCostUsd,
            'cost_formatted' => $isRegulerFree ? __('GRATIS') : CurrencyService::formatPrice($regulerCostUsd),
            'is_free' => $isRegulerFree,
            'badge' => $isRegulerFree ? 'Promo Gratis Ongkir' : 'Paling Populer',
            'available' => true,
        ];

        // Opsi 2: Express / Next Day (JNE YES / SiCepat BEST)
        $expressIdr = $zoneRates['express'];
        $expressCostUsd = round($expressIdr / max(1.0, $rateUsd), 2);
        $services[] = [
            'service' => 'express',
            'courier' => 'JNE YES / SiCepat BEST',
            'name' => 'Express Next Day (JNE YES / SiCepat BEST)',
            'description' => 'Jaminan sampai 1 hari kerja keesokan harinya (garansi ongkir kembali).',
            'eta' => $this->getEtaForZone('express', $zone),
            'cost_idr' => $expressIdr,
            'cost_usd' => $expressCostUsd,
            'cost_formatted' => CurrencyService::formatPrice($expressCostUsd),
            'is_free' => false,
            'badge' => 'Pasti Esok Tiba',
            'available' => true,
        ];

        // Opsi 3 & 4: Khusus wilayah Jabodetabek (Sameday & Instant)
        if ($isJabodetabek) {
            $samedayIdr = $zoneRates['sameday'] ?? 25000;
            $samedayCostUsd = round($samedayIdr / max(1.0, $rateUsd), 2);
            $services[] = [
                'service' => 'sameday',
                'courier' => 'GrabExpress / GoSend Sameday',
                'name' => 'Sameday Delivery (Grab / Gojek Sameday)',
                'description' => 'Barang tiba di hari yang sama (estimasi 6 - 8 jam setelah dikirim dari atelier).',
                'eta' => '6 - 8 Jam (Hari ini)',
                'cost_idr' => $samedayIdr,
                'cost_usd' => $samedayCostUsd,
                'cost_formatted' => CurrencyService::formatPrice($samedayCostUsd),
                'is_free' => false,
                'badge' => 'Tiba Hari Ini',
                'available' => true,
            ];

            $instantIdr = $zoneRates['instant'] ?? 40000;
            $instantCostUsd = round($instantIdr / max(1.0, $rateUsd), 2);
            $services[] = [
                'service' => 'instant',
                'courier' => 'GoSend / GrabExpress Instant',
                'name' => 'Instant Courier (GoSend / Grab Instant)',
                'description' => 'Driver kurir langsung menuju alamat Anda setelah pesanan dikemas (1-2 jam).',
                'eta' => '1 - 2 Jam Tiba',
                'cost_idr' => $instantIdr,
                'cost_usd' => $instantCostUsd,
                'cost_formatted' => CurrencyService::formatPrice($instantCostUsd),
                'is_free' => false,
                'badge' => 'Kilat 2 Jam',
                'available' => true,
            ];
        }

        return $services;
    }

    /**
     * Hitung tarif ongkos kirim berdasarkan pilihan spesifik, lokasi, dan subtotal pesanan.
     */
    public function calculate(string $service, ?string $province = null, ?string $city = null, float $subtotalUsd = 0.0, float $weightKg = 1.0): array
    {
        $service = strtolower(trim($service));
        $availableServices = $this->getAvailableServices($province, $city, $subtotalUsd, $weightKg);

        // Cari service yang diminta
        $matched = null;
        foreach ($availableServices as $opt) {
            if ($opt['service'] === $service) {
                $matched = $opt;
                break;
            }
        }

        // Jika tidak ditemukan (misal sameday di luar Jabodetabek), fallback ke reguler
        if (! $matched) {
            $matched = $availableServices[0];
            $service = $matched['service'];
        }

        // Live API query jika API key valid & dikonfigurasi
        $liveRate = $this->queryLiveCourierApi($city, (int) ($weightKg * 1000), $service);
        if ($liveRate !== null && ! ($matched['is_free'] ?? false)) {
            $rateUsd = app(CurrencyService::class)->getUsdToIdrRate();
            $matched['cost_idr'] = (int) $liveRate;
            $matched['cost_usd'] = round($liveRate / max(1.0, $rateUsd), 2);
            $matched['cost_formatted'] = CurrencyService::formatPrice($matched['cost_usd']);
        }

        return [
            'service' => $service,
            'name' => $matched['name'],
            'courier' => $matched['courier'],
            'eta' => $matched['eta'],
            'cost_usd' => $matched['cost_usd'],
            'cost_idr' => $matched['cost_idr'],
            'cost_formatted' => $matched['cost_formatted'],
            'is_free' => $matched['is_free'],
            'weight_kg' => $weightKg,
            'zone' => $this->determineZone($province, $city),
            'available_services' => $availableServices,
        ];
    }

    /**
     * Tentukan zona pengiriman logistik Indonesia.
     */
    public function determineZone(?string $province, ?string $city): string
    {
        $haystack = strtolower(trim(($province ?? '').' '.($city ?? '')));

        if (empty($haystack)) {
            return 'jabodetabek'; // Default Jakarta Atelier
        }

        // 1. Jabodetabek
        $jabodetabekKeywords = ['jakarta', 'dki', 'bogor', 'depok', 'tangerang', 'bekasi', 'tangsel', 'jabodetabek'];
        foreach ($jabodetabekKeywords as $kw) {
            if (str_contains($haystack, $kw)) {
                return 'jabodetabek';
            }
        }

        // 2. Pulau Jawa & Madura (Luar Jabodetabek)
        $jawaKeywords = ['jawa barat', 'jawa tengah', 'jawa timur', 'banten', 'yogyakarta', 'jogja', 'bandung', 'semarang', 'surabaya', 'solo', 'malang', 'cirebon', 'kediri', 'jember'];
        foreach ($jawaKeywords as $kw) {
            if (str_contains($haystack, $kw)) {
                return 'jawa';
            }
        }

        // 3. Bali & Nusa Tenggara
        $baliKeywords = ['bali', 'denpasar', 'badung', 'lombok', 'mataram', 'nusa tenggara', 'ntb', 'ntt', 'kupang'];
        foreach ($baliKeywords as $kw) {
            if (str_contains($haystack, $kw)) {
                return 'bali_nusra';
            }
        }

        // 4. Sumatera
        $sumateraKeywords = ['sumatera', 'medan', 'padang', 'palembang', 'riau', 'pekanbaru', 'batam', 'lampung', 'jambi', 'bengkulu', 'aceh', 'bangka', 'belitung'];
        foreach ($sumateraKeywords as $kw) {
            if (str_contains($haystack, $kw)) {
                return 'sumatera';
            }
        }

        // 5. Kalimantan
        $kalimantanKeywords = ['kalimantan', 'pontianak', 'banjarmasin', 'balikpapan', 'samarinda', 'palangka'];
        foreach ($kalimantanKeywords as $kw) {
            if (str_contains($haystack, $kw)) {
                return 'kalimantan';
            }
        }

        // 6. Sulawesi
        $sulawesiKeywords = ['sulawesi', 'makassar', 'manado', 'palu', 'kendari', 'gorontalo', 'mamuju'];
        foreach ($sulawesiKeywords as $kw) {
            if (str_contains($haystack, $kw)) {
                return 'sulawesi';
            }
        }

        // 7. Maluku & Papua
        $papuaKeywords = ['papua', 'maluku', 'ambon', 'jayapura', 'sorong', 'merauke', 'manokwari', 'timika', 'ternate'];
        foreach ($papuaKeywords as $kw) {
            if (str_contains($haystack, $kw)) {
                return 'maluku_papua';
            }
        }

        return 'jawa'; // Standar default domestik
    }

    /**
     * Tabel tarif resmi ekspedisi JNE/SiCepat per zona (dalam IDR).
     */
    protected function getZoneRates(string $zone, int $kg): array
    {
        return match ($zone) {
            'jabodetabek' => [
                'reguler' => 10000 * $kg,
                'express' => 18000 * $kg,
                'sameday' => 25000,
                'instant' => 40000,
            ],
            'jawa' => [
                'reguler' => 18000 * $kg,
                'express' => 28000 * $kg,
            ],
            'sumatera' => [
                'reguler' => 30000 * $kg,
                'express' => 48000 * $kg,
            ],
            'bali_nusra' => [
                'reguler' => 32000 * $kg,
                'express' => 52000 * $kg,
            ],
            'kalimantan' => [
                'reguler' => 40000 * $kg,
                'express' => 62000 * $kg,
            ],
            'sulawesi' => [
                'reguler' => 44000 * $kg,
                'express' => 68000 * $kg,
            ],
            'maluku_papua' => [
                'reguler' => 78000 * $kg,
                'express' => 125000 * $kg,
            ],
            default => [
                'reguler' => 20000 * $kg,
                'express' => 35000 * $kg,
            ],
        };
    }

    /**
     * Estimasi waktu pengiriman berdasarkan zona dan layanan.
     */
    protected function getEtaForZone(string $service, string $zone): string
    {
        if ($service === 'express') {
            return ($zone === 'maluku_papua') ? '1 - 2 Hari Kerja' : '1 Hari Kerja (Besok Tiba)';
        }

        return match ($zone) {
            'jabodetabek' => '1 - 2 Hari Kerja',
            'jawa' => '2 - 3 Hari Kerja',
            'sumatera', 'bali_nusra' => '2 - 4 Hari Kerja',
            'kalimantan', 'sulawesi' => '3 - 5 Hari Kerja',
            'maluku_papua' => '4 - 7 Hari Kerja',
            default => '2 - 4 Hari Kerja',
        };
    }

    /**
     * Query API live kurir (Biteship atau RajaOngkir) jika kredensial valid.
     */
    protected function queryLiveCourierApi(?string $destinationCity, int $weightGrams, string $service): ?float
    {
        // 1. Coba Biteship jika tersedia
        $biteshipKey = config('services.shipping.biteship_api_key');
        if (! empty($biteshipKey) && ! empty($destinationCity)) {
            $cost = $this->queryBiteship($biteshipKey, $destinationCity, $weightGrams, $service);
            if ($cost !== null) {
                return $cost;
            }
        }

        // 2. Coba RajaOngkir jika tersedia
        $rajaApiKey = config('services.shipping.rajaongkir_api_key');
        if (! empty($rajaApiKey) && ! empty($destinationCity)) {
            $cost = $this->queryRajaOngkir($rajaApiKey, $destinationCity, $weightGrams, $service);
            if ($cost !== null) {
                return $cost;
            }
        }

        return null;
    }

    /**
     * Query API Biteship Resmi.
     */
    protected function queryBiteship(string $apiKey, string $destinationCity, int $weightGrams, string $service): ?float
    {
        try {
            $response = Http::timeout(4)->withHeaders([
                'Authorization' => 'Bearer '.$apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.biteship.com/v1/rates/couriers', [
                'origin_postal_code' => 12190, // Jakarta Selatan Atelier
                'destination_area_id' => $destinationCity,
                'couriers' => 'jne,sicepat,jnt',
                'items' => [
                    [
                        'name' => 'Fashion Apparel',
                        'value' => 500000,
                        'weight' => max(250, $weightGrams),
                        'quantity' => 1,
                    ],
                ],
            ]);

            if ($response->successful()) {
                $pricing = $response->json('pricing', []);
                foreach ($pricing as $rate) {
                    $courierService = strtolower((string) ($rate['service_type'] ?? ''));
                    if ($service === 'express' && (str_contains($courierService, 'yes') || str_contains($courierService, 'best') || str_contains($courierService, 'overnight'))) {
                        return (float) ($rate['price'] ?? null);
                    }
                    if ($service === 'reguler' && (str_contains($courierService, 'reg') || str_contains($courierService, 'standard'))) {
                        return (float) ($rate['price'] ?? null);
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::info('Biteship API fallback: '.$e->getMessage());
        }

        return null;
    }

    /**
     * Query API RajaOngkir Resmi dengan validasi City ID.
     */
    protected function queryRajaOngkir(string $apiKey, string $destinationCity, int $weightGrams, string $service): ?float
    {
        try {
            $origin = (int) config('services.shipping.origin_city_id', 152);
            $destinationId = $this->resolveRajaOngkirCityId($destinationCity);

            if (! $destinationId) {
                return null;
            }

            $response = Http::timeout(4)->withHeaders([
                'key' => $apiKey,
            ])->post('https://api.rajaongkir.com/starter/cost', [
                'origin' => (string) $origin,
                'destination' => (string) $destinationId,
                'weight' => max(1000, $weightGrams),
                'courier' => 'jne',
            ]);

            if ($response->successful()) {
                $results = $response->json('rajaongkir.results.0.costs', []);
                foreach ($results as $item) {
                    $code = strtoupper((string) ($item['service'] ?? ''));
                    if ($service === 'express' && ($code === 'YES' || $code === 'BEST')) {
                        return (float) ($item['cost'][0]['value'] ?? null);
                    }
                    if ($service === 'reguler' && ($code === 'REG' || $code === 'EZ')) {
                        return (float) ($item['cost'][0]['value'] ?? null);
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::info('RajaOngkir API fallback: '.$e->getMessage());
        }

        return null;
    }

    /**
     * Konversi teks nama kota/kabupaten menjadi ID resmi RajaOngkir.
     */
    public function resolveRajaOngkirCityId(string $cityName): ?int
    {
        $normalized = strtolower(trim(preg_replace('/^(kota|kabupaten|kab\.|adm\.)\s+/i', '', $cityName)));

        if (is_numeric($normalized)) {
            return (int) $normalized;
        }

        if (array_key_exists($normalized, self::RAJAONGKIR_CITY_MAP)) {
            return self::RAJAONGKIR_CITY_MAP[$normalized];
        }

        foreach (self::RAJAONGKIR_CITY_MAP as $key => $id) {
            if (str_contains($normalized, $key) || str_contains($key, $normalized)) {
                return $id;
            }
        }

        return 152; // Default fallback Jakarta Pusat jika tidak terdeteksi
    }
}
