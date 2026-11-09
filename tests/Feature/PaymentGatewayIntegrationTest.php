<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentTransaction;
use App\Models\Product;
use App\Models\User;
use App\Services\Payment\Drivers\MidtransDriver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class PaymentGatewayIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Order $order;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'role' => 'customer',
        ]);

        $product = Product::factory()->create([
            'price' => 50.00,
            'stock' => 10,
        ]);

        $this->order = Order::create([
            'order_number' => 'ORD-TEST-'.uniqid(),
            'user_id' => $this->user->id,
            'customer_name' => 'Jane Luxury',
            'customer_email' => 'jane@shoppe-test.com',
            'customer_phone' => '081234567890',
            'shipping_address' => [
                'street' => 'Jl. Sudirman 10',
                'city' => 'Jakarta Pusat',
                'province' => 'DKI Jakarta',
                'postcode' => '10220',
                'country' => 'Indonesia',
            ],
            'subtotal' => 50.00,
            'shipping_cost' => 0.00,
            'tax' => 0.00,
            'grand_total' => 50.00,
            'payment_method' => 'midtrans',
            'payment_status' => 'unpaid',
            'status' => OrderStatus::PENDING,
        ]);

        OrderItem::create([
            'order_id' => $this->order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'unit_price' => 50.00,
            'quantity' => 1,
            'line_total' => 50.00,
        ]);
    }

    public function test_order_show_generates_payment_session(): void
    {
        config(['payment.default' => 'simulated']);

        $response = $this->get(route('order.show', $this->order->order_number));

        $response->assertStatus(200);
        $response->assertViewHas('paymentSession');
        $session = $response->viewData('paymentSession');

        $this->assertIsArray($session);
        $this->assertTrue($session['success']);
        $this->assertNotEmpty($session['token']);
        $this->assertSame('simulated', $session['driver']);
    }

    public function test_order_status_check_endpoint(): void
    {
        $response = $this->getJson(route('order.check_status', $this->order->order_number));

        $response->assertStatus(200)
            ->assertJson([
                'order_number' => $this->order->order_number,
                'payment_status' => 'unpaid',
                'status' => OrderStatus::PENDING->value,
                'is_paid' => false,
            ]);
    }

    public function test_order_initiate_pga_endpoint(): void
    {
        $response = $this->postJson(route('order.initiate_pga', $this->order->order_number), [
            'provider' => 'simulated',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $session = $response->json('session');
        $this->assertSame('simulated', $session['driver']);
        $this->assertNotEmpty($session['reference']);
    }

    public function test_webhook_rejects_unauthorized_signature(): void
    {
        $response = $this->postJson(route('payment.webhook', 'simulated'), [
            'order_number' => $this->order->order_number,
            'status' => 'settlement',
            'signature' => 'invalid_forged_signature',
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'status' => 'error',
                'message' => 'Invalid webhook signature.',
            ]);
    }

    public function test_webhook_successfully_settles_order_and_creates_transaction(): void
    {
        $transactionId = 'SIM-TRX-'.uniqid();
        $payload = [
            'order_number' => $this->order->order_number,
            'transaction_id' => $transactionId,
            'status' => 'settlement',
            'payment_channel' => 'bca_va',
            'amount' => 800000.0,
            'signature' => 'simulated_valid_signature',
        ];

        $response = $this->postJson(route('payment.webhook', 'simulated'), $payload);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'order_number' => $this->order->order_number,
                'payment_status' => 'settlement',
            ]);

        $this->order->refresh();
        $this->assertSame('paid', $this->order->payment_status);
        $this->assertSame(OrderStatus::PROCESSING, $this->order->status);
        $this->assertNotNull($this->order->paid_at);
        $this->assertSame('simulated', $this->order->payment_gateway);
        $this->assertSame('bca_va', $this->order->payment_channel);

        $this->assertDatabaseHas('payment_transactions', [
            'order_id' => $this->order->id,
            'transaction_id' => $transactionId,
            'provider' => 'simulated',
            'payment_channel' => 'bca_va',
            'status' => 'settlement',
        ]);
    }

    public function test_webhook_is_idempotent_on_duplicate_settlement(): void
    {
        $transactionId = 'SIM-TRX-IDEMPOTENT-01';
        $payload = [
            'order_number' => $this->order->order_number,
            'transaction_id' => $transactionId,
            'status' => 'settlement',
            'payment_channel' => 'qris',
            'amount' => 800000.0,
            'signature' => 'simulated_valid_signature',
        ];

        // Webhook pertama
        $res1 = $this->postJson(route('payment.webhook', 'simulated'), $payload);
        $res1->assertStatus(200);

        $this->order->refresh();
        $firstPaidAt = $this->order->paid_at;
        $this->assertNotNull($firstPaidAt);

        // Webhook kedua (retry dari gateway)
        $res2 = $this->postJson(route('payment.webhook', 'simulated'), $payload);
        $res2->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Webhook already processed (Idempotent).',
                'payment_status' => 'already_settled',
            ]);

        // Pastikan tidak ada duplikasi transaksi
        $this->assertSame(1, PaymentTransaction::where('transaction_id', $transactionId)->count());
    }

    public function test_webhook_handles_expired_order(): void
    {
        $payload = [
            'order_number' => $this->order->order_number,
            'transaction_id' => 'SIM-EXPIRE-'.uniqid(),
            'status' => 'expire',
            'signature' => 'simulated_valid_signature',
        ];

        $response = $this->postJson(route('payment.webhook', 'simulated'), $payload);

        $response->assertStatus(200);
        $this->order->refresh();
        $this->assertSame(OrderStatus::CANCELLED, $this->order->status);
        $this->assertSame('expire', $this->order->payment_status);
    }

    public function test_midtrans_driver_verifies_sha512_signature(): void
    {
        $serverKey = 'Mid-server-test-secret';
        $driver = new MidtransDriver([
            'server_key' => $serverKey,
            'client_key' => 'Mid-client-test-secret',
            'is_production' => false,
        ]);

        $orderId = 'ORD-MIDTRANS-99';
        $statusCode = '200';
        $grossAmount = '800000.00';
        $validSignature = hash('sha512', $orderId.$statusCode.$grossAmount.$serverKey);

        $request = Request::create('/payment/webhook/midtrans', 'POST', [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $validSignature,
        ]);

        $this->assertTrue($driver->verifyWebhookSignature($request));

        // Forged signature
        $requestForged = Request::create('/payment/webhook/midtrans', 'POST', [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => 'fake_hash_attempt',
        ]);

        $this->assertFalse($driver->verifyWebhookSignature($requestForged));
    }
}
