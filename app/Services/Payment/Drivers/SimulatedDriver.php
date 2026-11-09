<?php

declare(strict_types=1);

namespace App\Services\Payment\Drivers;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Order;
use Illuminate\Http\Request;

class SimulatedDriver implements PaymentGatewayInterface
{
    public function getDriverName(): string
    {
        return 'simulated';
    }

    public function createPaymentSession(Order $order): array
    {
        $token = 'SIM-TOKEN-'.strtoupper(uniqid()).'-'.$order->order_number;

        return [
            'success' => true,
            'reference' => $token,
            'token' => $token,
            'redirect_url' => route('order.show', $order->order_number).'?simulated_pay=1',
            'client_key' => 'simulated_client_key',
            'raw_response' => [
                'mode' => 'simulated',
                'token' => $token,
            ],
        ];
    }

    public function parseWebhook(Request $request): array
    {
        $payload = $request->all();

        return [
            'order_number' => (string) ($payload['order_number'] ?? $payload['order_id'] ?? ''),
            'transaction_id' => (string) ($payload['transaction_id'] ?? 'SIM-TRX-'.uniqid()),
            'status' => (string) ($payload['status'] ?? 'settlement'),
            'payment_channel' => (string) ($payload['payment_channel'] ?? 'qris_simulated'),
            'amount' => (float) ($payload['amount'] ?? 0),
            'signature' => (string) ($payload['signature'] ?? 'simulated_valid_signature'),
            'raw_payload' => $payload,
        ];
    }

    public function verifyWebhookSignature(Request $request): bool
    {
        // Dalam simulasi, signature dianggap sah jika bernilai 'simulated_valid_signature' atau 'valid'
        $sig = (string) $request->input('signature', '');

        return in_array($sig, ['simulated_valid_signature', 'valid', 'test_signature'], true);
    }
}
