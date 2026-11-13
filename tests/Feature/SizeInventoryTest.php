<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Services\CheckoutService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SizeInventoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_has_independent_size_stocks(): void
    {
        $product = Product::factory()->create([
            'size_stocks' => [
                'S' => 3,
                'M' => 10,
                'L' => 0,
                'XL' => 5,
            ],
        ]);

        $this->assertEquals(3, $product->getStockForSize('S'));
        $this->assertEquals(10, $product->getStockForSize('M'));
        $this->assertEquals(0, $product->getStockForSize('L'));
        $this->assertEquals(5, $product->getStockForSize('XL'));

        $this->assertTrue($product->hasStockForSize('S', 3));
        $this->assertFalse($product->hasStockForSize('S', 4));
        $this->assertFalse($product->hasStockForSize('L', 1));
    }

    public function test_checkout_decrements_only_the_purchased_size_stock(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'price' => 50,
            'stock' => 20,
            'size_stocks' => [
                'S' => 5,
                'M' => 10,
                'L' => 3,
                'XL' => 2,
            ],
        ]);

        $checkoutService = app(CheckoutService::class);

        $order = $checkoutService->processCheckout([
            'first_name' => 'Putri',
            'last_name' => 'Lestari',
            'email' => 'putri@example.com',
            'phone' => '08123456789',
            'street_address' => 'Jl. Senopati No. 12',
            'city' => 'Jakarta Selatan',
            'province' => 'DKI Jakarta',
            'postcode' => '12190',
            'payment_method' => 'bank_transfer',
        ], [
            [
                'id' => $product->id,
                'quantity' => 2,
                'size' => 'M',
            ],
        ], $user->id);

        $this->assertNotNull($order);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'size' => 'M',
        ]);

        $product->refresh();
        // M should be decremented from 10 to 8
        $this->assertEquals(8, $product->getStockForSize('M'));
        // Other sizes should remain unchanged
        $this->assertEquals(5, $product->getStockForSize('S'));
        $this->assertEquals(3, $product->getStockForSize('L'));
        $this->assertEquals(2, $product->getStockForSize('XL'));
    }

    public function test_checkout_fails_when_selected_size_is_out_of_stock(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'stock' => 10,
            'size_stocks' => [
                'S' => 0,
                'M' => 10,
            ],
        ]);

        $checkoutService = app(CheckoutService::class);

        $this->expectException(\Exception::class);

        $checkoutService->processCheckout([
            'first_name' => 'Siti',
            'last_name' => 'Aminah',
            'email' => 'siti@example.com',
            'street_address' => 'Jl. Kebon Jeruk',
            'city' => 'Jakarta Barat',
            'postcode' => '11530',
            'payment_method' => 'bank_transfer',
        ], [
            [
                'id' => $product->id,
                'quantity' => 1,
                'size' => 'S',
            ],
        ], $user->id);
    }

    public function test_customer_can_checkout_size_via_http(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'price' => 45,
            'stock' => 15,
            'size_stocks' => [
                'S' => 2,
                'M' => 8,
                'L' => 5,
            ],
        ]);

        $response = $this->actingAs($user)->post(route('checkout.process'), [
            'first_name' => 'Adinda',
            'last_name' => 'Pratiwi',
            'email' => 'adinda@example.com',
            'phone' => '0812999888',
            'country' => 'Indonesia',
            'province' => 'Jawa Barat',
            'city' => 'Bandung',
            'district' => 'Coblong',
            'postcode' => '40132',
            'street_address' => 'Jl. Dago No. 45',
            'payment_method' => 'bank_transfer',
            'items' => [
                [
                    'id' => $product->id,
                    'quantity' => 1,
                    'size' => 'L',
                ],
            ],
        ]);

        $response->assertRedirect();
        $product->refresh();
        $this->assertEquals(4, $product->getStockForSize('L'));
        $this->assertEquals(2, $product->getStockForSize('S'));
        $this->assertEquals(8, $product->getStockForSize('M'));
    }
}
