<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 25, 180);

        return [
            'user_id' => User::factory(),
            'order_number' => 'SHP-'.strtoupper(Str::random(8)),
            'status' => OrderStatus::PENDING,
            'payment_status' => 'unpaid',
            'subtotal' => $subtotal,
            'shipping_cost' => 0.00,
            'discount_amount' => 0.00,
            'grand_total' => $subtotal,
            'customer_name' => fake()->name(),
            'customer_email' => fake()->safeEmail(),
            'customer_phone' => '0812'.fake()->numerify('########'),
            'shipping_address' => [
                'label' => 'Rumah',
                'country' => 'Indonesia',
                'province' => 'DKI Jakarta',
                'city' => 'Jakarta Selatan',
                'district' => 'Kebayoran Baru',
                'postcode' => '12190',
                'street' => fake()->streetAddress(),
            ],
            'payment_method' => 'bank_transfer',
        ];
    }
}
