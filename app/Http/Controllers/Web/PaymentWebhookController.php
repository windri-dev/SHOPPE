<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\Payment\PaymentGatewayManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentWebhookController extends Controller
{
    public function __construct(
        protected PaymentGatewayManager $paymentManager
    ) {}

    /**
     * Endpoint penerima callback Webhook dari Payment Gateway Aggregator (Midtrans, Xendit, Simulated, dll).
     */
    public function handle(Request $request, ?string $provider = null): JsonResponse
    {
        $result = $this->paymentManager->handleWebhook($request, $provider);

        if (! $result['success'] && $result['status'] === 'unauthorized') {
            return response()->json([
                'status' => 'error',
                'message' => $result['message'],
            ], 403);
        }

        if (! $result['success'] && $result['status'] === 'not_found') {
            return response()->json([
                'status' => 'error',
                'message' => $result['message'],
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => $result['message'],
            'order_number' => $result['order_number'],
            'payment_status' => $result['status'],
        ], 200);
    }
}
