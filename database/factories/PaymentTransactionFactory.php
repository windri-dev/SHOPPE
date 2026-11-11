<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Order;
use App\Models\PaymentTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<PaymentTransaction>
 */
class PaymentTransactionFactory extends Factory
{
    protected $model = PaymentTransaction::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'provider' => 'midtrans',
            'transaction_id' => 'TRX-'.Str::upper(Str::random(12)),
            'payment_channel' => 'qris',
            'amount' => fake()->randomFloat(2, 20, 200),
            'status' => 'settlement',
            'signature' => hash('sha512', Str::random(32)),
            'payload_response' => ['status_code' => '200', 'transaction_status' => 'settlement'],
        ];
    }
}
