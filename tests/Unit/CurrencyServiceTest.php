<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Product;
use App\Services\CurrencyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CurrencyServiceTest extends TestCase
{
    use RefreshDatabase;

    private CurrencyService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(CurrencyService::class);
        Cache::forget('currency_rate_usd_to_idr');
    }

    public function test_it_fetches_live_rate_from_api_and_caches_it(): void
    {
        Http::fake([
            'open.er-api.com/*' => Http::response([
                'result' => 'success',
                'rates' => [
                    'IDR' => 16250.00,
                ],
            ], 200),
        ]);

        $rate = $this->service->getUsdToIdrRate();

        $this->assertEquals(16250.00, $rate);
        $this->assertTrue(Cache::has('currency_rate_usd_to_idr'));
    }

    public function test_it_uses_fallback_rate_if_api_fails(): void
    {
        Http::fake([
            'open.er-api.com/*' => Http::response([], 500),
        ]);

        config(['services.currency.default_usd_to_idr' => 16000.0]);

        $rate = $this->service->getUsdToIdrRate();

        $this->assertEquals(16000.0, $rate);
    }

    public function test_it_formats_price_in_usd_for_en_locale(): void
    {
        $formatted = $this->service->format(25.50, 'en');

        $this->assertEquals('$ 25.50', $formatted);
    }

    public function test_it_formats_price_in_idr_with_elegant_thousand_rounding_for_id_locale(): void
    {
        Cache::put('currency_rate_usd_to_idr', 16000.0, 86400);

        // 20 USD * 16,000 = 320,000
        $formatted = $this->service->format(20.00, 'id');

        $this->assertEquals('Rp 320.000', $formatted);
    }

    public function test_product_model_formatted_price_adapts_to_active_locale(): void
    {
        Cache::put('currency_rate_usd_to_idr', 16000.0, 86400);

        $category = Category::create([
            'name' => 'Tops',
            'slug' => 'tops',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'sku' => 'SKU-CURR-1',
            'name' => 'Silk Blouse',
            'slug' => 'silk-blouse',
            'price' => 20.00,
            'stock' => 10,
            'images' => ['images/product-1.png'],
        ]);

        // When in ID locale
        app()->setLocale('id');
        $this->assertEquals('Rp 320.000', $product->formatted_price);

        // When in EN locale
        app()->setLocale('en');
        $this->assertEquals('$ 20.00', $product->formatted_price);
    }
}
