<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CurrencyService
{
    protected const CACHE_KEY = 'currency_rate_usd_to_idr';

    /**
     * Dapatkan nilai kurs 1 USD ke IDR (Rupiah).
     * Di-cache selama 24 jam untuk kecepatan maksimal, dengan fallback anggun.
     */
    public function getUsdToIdrRate(): float
    {
        $cacheTtl = (int) config('services.currency.cache_ttl', 86400);

        return (float) Cache::remember(self::CACHE_KEY, $cacheTtl, function (): float {
            return $this->fetchLiveRate();
        });
    }

    public function getExchangeRate(): float
    {
        return $this->getUsdToIdrRate();
    }

    /**
     * Format nominal USD ke teks harga sesuai locale aktif (ID: Rp xxx.xxx, EN: $ xx.xx).
     */
    public function format(float|int|string|null $usdAmount, ?string $locale = null): string
    {
        $amount = (float) ($usdAmount ?? 0.0);
        $activeLocale = $locale ?? app()->getLocale();

        if ($activeLocale === 'id') {
            $rate = $this->getUsdToIdrRate();
            $idrAmount = $amount * $rate;

            // Pembulatan mewah khas butik ke ribuan terdekat (e.g. Rp 350.000)
            $roundedIdr = (float) (round($idrAmount / 1000) * 1000);

            return 'Rp '.number_format($roundedIdr, 0, ',', '.');
        }

        // Default English / International USD format
        return '$ '.number_format($amount, 2, '.', ',');
    }

    /**
     * Helper statis cepat untuk formatting harga.
     */
    public static function formatPrice(float|int|string|null $usdAmount, ?string $locale = null): string
    {
        return app(self::class)->format($usdAmount, $locale);
    }

    /**
     * Konversi nilai USD ke mata uang target dalam bentuk angka (float).
     */
    public function convert(float $usdAmount, string $targetCurrency = 'IDR'): float
    {
        if (strtoupper($targetCurrency) === 'IDR') {
            $rate = $this->getUsdToIdrRate();

            return (float) (round(($usdAmount * $rate) / 1000) * 1000);
        }

        return $usdAmount;
    }

    /**
     * Paksa perbarui nilai kurs dari API eksternal dan perbarui cache.
     */
    public function refreshRates(): float
    {
        Cache::forget(self::CACHE_KEY);

        $freshRate = $this->fetchLiveRate();
        $cacheTtl = (int) config('services.currency.cache_ttl', 86400);
        Cache::put(self::CACHE_KEY, $freshRate, $cacheTtl);

        return $freshRate;
    }

    /**
     * Ambil data kurs harian dari ExchangeRate API terbuka.
     */
    protected function fetchLiveRate(): float
    {
        $defaultRate = (float) config('services.currency.default_usd_to_idr', 16000.0);
        $apiUrl = (string) config('services.currency.api_url', 'https://open.er-api.com/v6/latest/USD');

        try {
            $httpClient = Http::timeout(5);
            if (app()->environment('local', 'testing')) {
                $httpClient = $httpClient->withoutVerifying();
            }

            $response = $httpClient->get($apiUrl);

            if ($response->successful()) {
                $data = $response->json();
                $rate = $data['rates']['IDR'] ?? null;

                if (is_numeric($rate) && (float) $rate > 1000) {
                    return (float) $rate;
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Gagal mengambil kurs live exchange rate, menggunakan fallback: '.$e->getMessage());
        }

        return $defaultRate;
    }
}
