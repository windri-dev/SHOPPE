<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Checkout\ProcessCheckoutRequest;
use App\Services\CheckoutService;
use App\Services\Shipping\ShippingCalculationService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        protected CheckoutService $checkoutService
    ) {}

    public function index(): View
    {
        return view('pages.checkout');
    }

    public function process(ProcessCheckoutRequest $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();

        try {
            $userId = auth()->id();
            $order = $this->checkoutService->processCheckout(
                $validated,
                $validated['items'],
                $userId ? (int) $userId : null
            );

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pesanan Anda berhasil dibuat!',
                    'order_number' => $order->order_number,
                    'redirect_url' => route('order.show', $order->order_number),
                ]);
            }

            return redirect()
                ->route('order.show', $order->order_number)
                ->with('success', 'Pesanan Anda berhasil dibuat!');
        } catch (Exception $e) {
            Log::channel('transactions')->error('Checkout transaction failed: '.$e->getMessage(), [
                'user_id' => auth()->id(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'payment_method' => $validated['payment_method'] ?? 'unknown',
                'subtotal' => $validated['subtotal'] ?? 0,
                'shipping_cost' => $validated['shipping_cost'] ?? 0,
                'items_count' => count($validated['items'] ?? []),
                'items' => array_map(fn ($item) => [
                    'product_id' => $item['product_id'] ?? null,
                    'size' => $item['size'] ?? null,
                    'quantity' => $item['quantity'] ?? null,
                ], $validated['items'] ?? []),
                'exception' => $e::class,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Hitung tarif ongkir kurir pengiriman secara dinamis via AJAX.
     */
    public function calculateShipping(Request $request, ShippingCalculationService $shippingService): JsonResponse
    {
        $validated = $request->validate([
            'service' => 'required|string|in:reguler,express,sameday,instant',
            'province' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'subtotal' => 'nullable|numeric|min:0',
            'weight_kg' => 'nullable|numeric|min:0.1',
        ]);

        $result = $shippingService->calculate(
            service: $validated['service'],
            province: $validated['province'] ?? null,
            city: $validated['city'] ?? null,
            subtotalUsd: (float) ($validated['subtotal'] ?? 0.0),
            weightKg: (float) ($validated['weight_kg'] ?? 1.0)
        );

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }
}
