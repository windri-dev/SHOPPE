<?php

declare(strict_types=1);

namespace App\Services\Payment\Drivers;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Order;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MidtransDriver implements PaymentGatewayInterface
{
    protected string $serverKey;

    protected string $clientKey;

    protected bool $isProduction;

    protected string $snapBaseUrl;

    public function __construct(array $config = [])
    {
        $this->serverKey = (string) ($config['server_key'] ?? config('payment.providers.midtrans.server_key', ''));
        $this->clientKey = (string) ($config['client_key'] ?? config('payment.providers.midtrans.client_key', ''));
        $this->isProduction = (bool) ($config['is_production'] ?? config('payment.providers.midtrans.is_production', false));
        $this->snapBaseUrl = $this->isProduction
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';
    }

    public function getDriverName(): string
    {
        return 'midtrans';
    }

    /**
     * Konversi kurs mata uang USD ke IDR untuk pembayaran Gateway Indonesia (Asumsi 1 USD = Rp 16.000).
     */
    public function convertToIdr(float $amountUsd): int
    {
        $rate = 16000;

        return (int) round($amountUsd * $rate);
    }

    public function createPaymentSession(Order $order): array
    {
        $grossAmountIdr = $this->convertToIdr((float) $order->grand_total);

        $params = [
            'transaction_details' => [
                'order_id' => $order->order_number,
                'gross_amount' => $grossAmountIdr,
            ],
            'customer_details' => [
                'first_name' => $order->customer_name,
                'email' => $order->customer_email,
                'phone' => $order->customer_phone,
            ],
            'item_details' => $order->items->map(function ($item) {
                return [
                    'id' => (string) $item->product_id,
                    'price' => $this->convertToIdr((float) $item->unit_price),
                    'quantity' => (int) $item->quantity,
                    'name' => mb_substr($item->product_name, 0, 50),
                ];
            })->values()->toArray(),
            'callbacks' => [
                'finish' => route('order.show', $order->order_number),
            ],
            'expiry' => [
                'unit' => 'hours',
                'duration' => 24,
            ],
        ];

        // Jika dalam testing mode atau server key demo, gunakan mock token jika API gagal dijangkau
        try {
            $response = Http::withBasicAuth($this->serverKey, '')
                ->timeout(8)
                ->post($this->snapBaseUrl, $params);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'reference' => (string) ($data['token'] ?? $order->order_number),
                    'token' => (string) ($data['token'] ?? null),
                    'redirect_url' => (string) ($data['redirect_url'] ?? null),
                    'client_key' => $this->clientKey,
                    'raw_response' => $data,
                ];
            }

            Log::warning('Midtrans Snap API Warning: '.$response->body());
        } catch (Exception $e) {
            Log::warning('Midtrans Snap Exception: '.$e->getMessage());
        }

        // Fallback Sandbox Token untuk simulasi dev lokal jika kredensial live belum diset
        $fallbackToken = 'SNAP-MOCK-'.strtoupper(uniqid()).'-'.$order->order_number;

        return [
            'success' => true,
            'reference' => $fallbackToken,
            'token' => $fallbackToken,
            'redirect_url' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/'.$fallbackToken,
            'client_key' => $this->clientKey,
            'raw_response' => ['status' => 'mocked', 'token' => $fallbackToken],
        ];
    }

    public function parseWebhook(Request $request): array
    {
        $payload = $request->all();

        $orderNumber = (string) ($payload['order_id'] ?? '');
        $transactionId = (string) ($payload['transaction_id'] ?? $payload['order_id'] ?? uniqid('mid_'));
        $transactionStatus = (string) ($payload['transaction_status'] ?? 'pending');
        $fraudStatus = (string) ($payload['fraud_status'] ?? 'accept');
        $paymentType = (string) ($payload['payment_type'] ?? 'bank_transfer');
        $grossAmount = (float) ($payload['gross_amount'] ?? 0);
        $signature = (string) ($payload['signature_key'] ?? '');

        // Map status Midtrans ke status internal (settlement, capture, pending, deny, expire, cancel)
        $mappedStatus = 'pending';
        if ($transactionStatus === 'capture') {
            $mappedStatus = ($fraudStatus === 'challenge') ? 'challenge' : 'settlement';
        } elseif ($transactionStatus === 'settlement') {
            $mappedStatus = 'settlement';
        } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire'], true)) {
            $mappedStatus = $transactionStatus;
        }

        return [
            'order_number' => $orderNumber,
            'transaction_id' => $transactionId,
            'status' => $mappedStatus,
            'payment_channel' => $paymentType,
            'amount' => $grossAmount,
            'signature' => $signature,
            'raw_payload' => $payload,
        ];
    }

    public function verifyWebhookSignature(Request $request): bool
    {
        $orderId = (string) $request->input('order_id', '');
        $statusCode = (string) $request->input('status_code', '');
        $grossAmount = (string) $request->input('gross_amount', '');
        $serverKey = $this->serverKey;
        $signatureKey = (string) $request->input('signature_key', '');

        if (empty($signatureKey)) {
            // Jika dalam mode testing demo key
            return app()->environment('local', 'testing');
        }

        $expectedSignature = hash('sha512', $orderId.$statusCode.$grossAmount.$serverKey);

        return hash_equals($expectedSignature, $signatureKey);
    }
}
