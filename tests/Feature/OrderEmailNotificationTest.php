<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Mail\OrderInvoiceMail;
use App\Mail\OrderShippedMail;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OrderEmailNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_invoice_email_is_sent_on_successful_checkout(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $product = Product::factory()->create([
            'name' => 'Aurelia Silk Wrap Dress',
            'price' => 65,
            'stock' => 10,
            'size_stocks' => ['M' => 5],
        ]);

        $response = $this->actingAs($user)->post(route('checkout.process'), [
            'first_name' => 'Clara',
            'last_name' => 'Sartika',
            'email' => 'clara@example.com',
            'phone' => '081288887777',
            'country' => 'Indonesia',
            'province' => 'DKI Jakarta',
            'city' => 'Jakarta Selatan',
            'postcode' => '12190',
            'street_address' => 'Jl. Senopati Suites No. 8',
            'payment_method' => 'bank_transfer',
            'items' => [
                [
                    'id' => $product->id,
                    'quantity' => 1,
                    'size' => 'M',
                ],
            ],
        ]);

        $response->assertRedirect();

        Mail::assertSent(OrderInvoiceMail::class);
        Mail::assertSent(OrderInvoiceMail::class, function (OrderInvoiceMail $mail) {
            return $mail->hasTo('clara@example.com');
        });

        $order = Order::first();
        $mail = new OrderInvoiceMail($order->load('items'));
        $rendered = $mail->render();
        $this->assertStringContainsString('PUTRIJALVA', $rendered);
        $this->assertStringContainsString('Clara Sartika', $rendered);
        $this->assertStringContainsString('Aurelia Silk Wrap Dress', $rendered);
        $this->assertStringContainsString('Ukuran: M', $rendered);
        $this->assertStringContainsString('8830-1289-00', $rendered); // BCA account
    }

    public function test_order_shipped_email_is_sent_when_admin_updates_status(): void
    {
        Mail::fake();

        $admin = User::factory()->create(['role' => 'admin']);
        $order = Order::factory()->create([
            'customer_name' => 'Farah Quinn',
            'customer_email' => 'farah@example.com',
            'status' => OrderStatus::PROCESSING,
            'tracking_number' => 'JNE-882910293',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.orders.status', $order), [
            'status' => 'shipped',
        ]);

        $response->assertRedirect();

        Mail::assertSent(OrderShippedMail::class);
        Mail::assertSent(OrderShippedMail::class, function (OrderShippedMail $mail) use ($order) {
            return $mail->hasTo('farah@example.com') && $mail->order->id === $order->id;
        });

        $mail = new OrderShippedMail($order);
        $rendered = $mail->render();
        $this->assertStringContainsString('Farah Quinn', $rendered);
        $this->assertStringContainsString('JNE-882910293', $rendered);
    }

    public function test_order_shipped_email_is_sent_when_tracking_number_is_provided(): void
    {
        Mail::fake();

        $admin = User::factory()->create(['role' => 'admin']);
        $order = Order::factory()->create([
            'customer_name' => 'Dian Sastro',
            'customer_email' => 'dian@example.com',
            'status' => OrderStatus::PROCESSING,
            'payment_status' => 'paid',
            'tracking_number' => null,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.orders.payment', $order), [
            'payment_status' => 'paid',
            'tracking_number' => 'SICEPAT-7729104',
        ]);

        $response->assertRedirect();

        Mail::assertSent(OrderShippedMail::class);
        Mail::assertSent(OrderShippedMail::class, function (OrderShippedMail $mail) {
            return $mail->hasTo('dian@example.com');
        });
    }
}
