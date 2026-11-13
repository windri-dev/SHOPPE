<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Services\Notification\WhatsAppNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class WhatsAppNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_phone_number_formatting_normalizes_indonesian_prefixes(): void
    {
        $this->assertEquals('6281234567890', WhatsAppNotificationService::formatPhone('081234567890'));
        $this->assertEquals('6281234567890', WhatsAppNotificationService::formatPhone('6281234567890'));
        $this->assertEquals('6281234567890', WhatsAppNotificationService::formatPhone('+62 812-3456-7890'));
        $this->assertEquals('6281234567890', WhatsAppNotificationService::formatPhone('81234567890'));
        $this->assertNull(WhatsAppNotificationService::formatPhone(null));
        $this->assertNull(WhatsAppNotificationService::formatPhone(''));
    }

    public function test_generates_direct_concierge_whatsapp_url_for_order(): void
    {
        $order = Order::create([
            'order_number' => 'SHP-TESTWA01',
            'status' => OrderStatus::PENDING,
            'subtotal' => 100.0,
            'shipping_cost' => 0.0,
            'discount_amount' => 0.0,
            'grand_total' => 100.0,
            'customer_name' => 'Clara Oswald',
            'customer_email' => 'clara@example.com',
            'customer_phone' => '081234567890',
            'shipping_address' => ['street' => 'Jl. Thamrin'],
            'payment_method' => 'bank_transfer',
        ]);

        $service = app(WhatsAppNotificationService::class);
        $url = $service->generateOrderConfirmationUrl($order);

        $this->assertStringContainsString('https://wa.me/', $url);
        $this->assertStringContainsString('SHP-TESTWA01', $url);
        $this->assertStringContainsString('Clara%20Oswald', $url);
    }

    public function test_generates_customer_chat_url_for_admin(): void
    {
        $order = Order::create([
            'order_number' => 'SHP-TESTWA02',
            'status' => OrderStatus::PROCESSING,
            'subtotal' => 50.0,
            'shipping_cost' => 0.0,
            'discount_amount' => 0.0,
            'grand_total' => 50.0,
            'customer_name' => 'Rose Tyler',
            'customer_email' => 'rose@example.com',
            'customer_phone' => '089876543210',
            'shipping_address' => ['street' => 'Jl. Sudirman'],
            'payment_method' => 'qris',
        ]);

        $service = app(WhatsAppNotificationService::class);
        $adminChatUrl = $service->generateCustomerChatUrl($order);

        $this->assertNotNull($adminChatUrl);
        $this->assertStringContainsString('6289876543210', $adminChatUrl);
        $this->assertStringContainsString('SHP-TESTWA02', $adminChatUrl);
    }

    public function test_order_notification_dispatches_cleanly(): void
    {
        Log::spy();

        $order = Order::create([
            'order_number' => 'SHP-NOTIF01',
            'status' => OrderStatus::PENDING,
            'subtotal' => 75.0,
            'shipping_cost' => 0.0,
            'discount_amount' => 0.0,
            'grand_total' => 75.0,
            'customer_name' => 'Donna Noble',
            'customer_email' => 'donna@example.com',
            'customer_phone' => '08111222333',
            'shipping_address' => ['street' => 'Jl. Gatot Subroto'],
            'payment_method' => 'bank_transfer',
        ]);

        $service = app(WhatsAppNotificationService::class);
        $result = $service->sendOrderNotification($order);

        $this->assertTrue($result);
        Log::shouldHaveReceived('info')->atLeast()->once();
    }

    public function test_shipping_notification_dispatches_with_tracking_number(): void
    {
        Log::spy();

        $order = Order::create([
            'order_number' => 'SHP-SHIP01',
            'status' => OrderStatus::SHIPPED,
            'tracking_number' => 'JNE-REG-99887766',
            'subtotal' => 90.0,
            'shipping_cost' => 0.0,
            'discount_amount' => 0.0,
            'grand_total' => 90.0,
            'customer_name' => 'Amy Pond',
            'customer_email' => 'amy@example.com',
            'customer_phone' => '085566778899',
            'shipping_address' => ['street' => 'Jl. Rasuna Said'],
            'payment_method' => 'credit_card',
        ]);

        $service = app(WhatsAppNotificationService::class);
        $result = $service->sendShippingNotification($order);

        $this->assertTrue($result);
        Log::shouldHaveReceived('info')->atLeast()->once();
    }
}
