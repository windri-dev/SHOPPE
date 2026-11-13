<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Contracts\PaymentGatewayInterface;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Services\Payment\Drivers\MidtransDriver;
use App\Services\Payment\Drivers\SimulatedDriver;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class PaymentGatewayManager
{
    /** @var array<string, PaymentGatewayInterface> */
    protected array $drivers = [];

    /**
     * Dapatkan instance driver gateway yang diminta atau default.
     */
    public function driver(?string $name = null): PaymentGatewayInterface
    {
        $driverName = $name ?: config('payment.default', 'midtrans');

        if (! isset($this->drivers[$driverName])) {
            $this->drivers[$driverName] = $this->resolveDriver($driverName);
        }

        return $this->drivers[$driverName];
    }

    /**
     * Inisialisasi instance driver berdasarkan nama.
     */
    protected function resolveDriver(string $name): PaymentGatewayInterface
    {
        return match ($name) {
            'midtrans' => new MidtransDriver(config('payment.providers.midtrans', [])),
            'simulated' => new SimulatedDriver,
            default => throw new InvalidArgumentException("Payment gateway driver [{$name}] tidak didukung."),
        };
    }

    /**
     * Daftarkan kustom driver secara dinamis (untuk testing atau extension).
     */
    public function extend(string $name, PaymentGatewayInterface $driver): self
    {
        $this->drivers[$name] = $driver;

        return $this;
    }

    /**
     * Buat sesi pembayaran untuk order tertentu menggunakan driver aktif.
     *
     * @return array{
     *     success: bool,
     *     reference: string,
     *     token: string|null,
     *     redirect_url: string|null,
     *     client_key: string|null,
     *     driver: string
     * }
     */
    public function initiatePayment(Order $order, ?string $provider = null): array
    {
        $gateway = $this->driver($provider);
        $session = $gateway->createPaymentSession($order);

        // Update informasi gateway pada order
        $order->update([
            'payment_gateway' => $gateway->getDriverName(),
            'payment_reference' => $session['reference'] ?? null,
            'payment_url' => $session['redirect_url'] ?? null,
        ]);

        return array_merge($session, [
            'driver' => $gateway->getDriverName(),
        ]);
    }

    /**
     * Proses webhook callback masuk dengan verifikasi signature, idempotency, dan DB transaction.
     *
     * @return array{success: bool, message: string, order_number: string|null, status: string}
     */
    public function handleWebhook(Request $request, ?string $provider = null): array
    {
        $gateway = $this->driver($provider);

        // 1. Validasi Keaslian Signature Webhook
        if (! $gateway->verifyWebhookSignature($request)) {
            Log::channel('transactions')->warning("Payment Gateway Webhook Invalid Signature [{$gateway->getDriverName()}]: ", $request->all());
            Log::warning("Payment Gateway Webhook Invalid Signature [{$gateway->getDriverName()}]: ", $request->all());

            return [
                'success' => false,
                'message' => 'Invalid webhook signature.',
                'order_number' => null,
                'status' => 'unauthorized',
            ];
        }

        // 2. Parse Payload
        $parsed = $gateway->parseWebhook($request);
        $orderNumber = $parsed['order_number'];

        if (empty($orderNumber)) {
            return [
                'success' => false,
                'message' => 'Order number not present in webhook payload.',
                'order_number' => null,
                'status' => 'bad_request',
            ];
        }

        // 3. Eksekusi Atomic Transaction & Idempotency
        return DB::transaction(function () use ($parsed, $gateway, $orderNumber) {
            /** @var Order|null $order */
            $order = Order::where('order_number', $orderNumber)->lockForUpdate()->first();

            if (! $order) {
                return [
                    'success' => false,
                    'message' => "Order [{$orderNumber}] not found.",
                    'order_number' => $orderNumber,
                    'status' => 'not_found',
                ];
            }

            // Cek Idempotency: Jika transaksi spesifik ini sudah pernah dicatat settlement
            $existingTx = PaymentTransaction::where('provider', $gateway->getDriverName())
                ->where('transaction_id', $parsed['transaction_id'])
                ->first();

            if ($existingTx && $existingTx->status === 'settlement' && $order->isPaid()) {
                return [
                    'success' => true,
                    'message' => 'Webhook already processed (Idempotent).',
                    'order_number' => $orderNumber,
                    'status' => 'already_settled',
                ];
            }

            // Simpan / update catatan PaymentTransaction untuk audit trail
            $transaction = PaymentTransaction::updateOrCreate(
                [
                    'provider' => $gateway->getDriverName(),
                    'transaction_id' => $parsed['transaction_id'],
                ],
                [
                    'order_id' => $order->id,
                    'payment_channel' => $parsed['payment_channel'],
                    'amount' => $parsed['amount'] > 0 ? $parsed['amount'] : (float) $order->grand_total,
                    'status' => $parsed['status'],
                    'signature' => $parsed['signature'],
                    'payload_response' => $parsed['raw_payload'],
                ]
            );

            // Perbarui status pesanan berdasarkan hasil dari payment gateway
            if ($parsed['status'] === 'settlement') {
                $order->update([
                    'payment_status' => 'paid',
                    'paid_at' => Carbon::now(),
                    'status' => OrderStatus::PROCESSING,
                    'payment_gateway' => $order->payment_gateway ?: $gateway->getDriverName(),
                    'payment_channel' => $parsed['payment_channel'] ?? $order->payment_channel,
                ]);

                Log::channel('transactions')->info("Payment Gateway Order [{$orderNumber}] successfully SETTLED via {$gateway->getDriverName()} ({$parsed['payment_channel']})", [
                    'order_number' => $orderNumber,
                    'provider' => $gateway->getDriverName(),
                    'channel' => $parsed['payment_channel'] ?? null,
                    'transaction_id' => $transaction->id,
                ]);
                Log::info("Payment Gateway Order [{$orderNumber}] successfully SETTLED via {$gateway->getDriverName()} ({$parsed['payment_channel']})");
            } elseif (in_array($parsed['status'], ['deny', 'cancel', 'expire'], true)) {
                $order->update([
                    'payment_status' => $parsed['status'],
                    'status' => OrderStatus::CANCELLED,
                    'payment_gateway' => $order->payment_gateway ?: $gateway->getDriverName(),
                ]);

                Log::channel('transactions')->info("Payment Gateway Order [{$orderNumber}] marked as {$parsed['status']}", [
                    'order_number' => $orderNumber,
                    'provider' => $gateway->getDriverName(),
                    'status' => $parsed['status'],
                    'transaction_id' => $transaction->id,
                ]);
                Log::info("Payment Gateway Order [{$orderNumber}] marked as {$parsed['status']}");
            }

            return [
                'success' => true,
                'message' => "Order [{$orderNumber}] payment status updated to {$parsed['status']}.",
                'order_number' => $orderNumber,
                'status' => $parsed['status'],
            ];
        });
    }
}
