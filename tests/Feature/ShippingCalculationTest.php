<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Services\CurrencyService;
use App\Services\Shipping\ShippingCalculationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShippingCalculationTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_available_services_includes_all_for_jabodetabek(): void
    {
        $service = app(ShippingCalculationService::class);
        $services = $service->getAvailableServices('DKI Jakarta', 'Jakarta Selatan');

        $codes = array_column($services, 'service');
        $this->assertContains('reguler', $codes);
        $this->assertContains('express', $codes);
        $this->assertContains('sameday', $codes);
        $this->assertContains('instant', $codes);
    }

    public function test_get_available_services_excludes_sameday_outside_jabodetabek(): void
    {
        $service = app(ShippingCalculationService::class);
        $services = $service->getAvailableServices('Bali', 'Denpasar');

        $codes = array_column($services, 'service');
        $this->assertContains('reguler', $codes);
        $this->assertContains('express', $codes);
        $this->assertNotContains('sameday', $codes);
        $this->assertNotContains('instant', $codes);
    }

    public function test_calculate_regular_shipping_is_free(): void
    {
        $service = app(ShippingCalculationService::class);
        $calc = $service->calculate('reguler', 'Jawa Barat', 'Bandung', 50.0);

        $this->assertEquals(0.0, $calc['cost_usd']);
        $this->assertTrue($calc['is_free']);
    }

    public function test_calculate_express_shipping_returns_rate(): void
    {
        $service = app(ShippingCalculationService::class);
        $calc = $service->calculate('express', 'Jawa Timur', 'Surabaya', 40.0);

        $expectedUsd = round(28000 / app(CurrencyService::class)->getUsdToIdrRate(), 2);

        $this->assertEquals(28000, $calc['cost_idr']);
        $this->assertEquals($expectedUsd, $calc['cost_usd']);
        $this->assertFalse($calc['is_free']);
    }

    public function test_calculate_endpoint_returns_json_response(): void
    {
        $response = $this->postJson(route('shipping.calculate'), [
            'service' => 'express',
            'province' => 'DKI Jakarta',
            'city' => 'Jakarta Pusat',
            'subtotal' => 100.0,
            'weight_kg' => 1.5,
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.service', 'express');

        // 1.5 kg dibulatkan ke 2 kg x Rp 18.000 (tarif express Jabodetabek).
        $expectedUsd = round(36000 / app(CurrencyService::class)->getUsdToIdrRate(), 2);

        $this->assertEquals(36000, (int) $response->json('data.cost_idr'));
        $this->assertEquals($expectedUsd, (float) $response->json('data.cost_usd'));
    }
}
