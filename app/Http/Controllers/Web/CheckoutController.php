<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\CheckoutService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Exception;

class CheckoutController extends Controller
{
    public function index(): View
    {
        return view('pages.checkout');
    }

    public function process(Request $request, CheckoutService $checkoutService)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'phone' => 'nullable|string|max:30',
            'country' => 'required|string',
            'street_address' => 'required|string',
            'city' => 'required|string',
            'postcode' => 'required|string',
            'payment_method' => 'required|string',
            'shipping_cost' => 'nullable|numeric',
            'discount_amount' => 'nullable|numeric',
            'order_notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            $userId = auth()->id();
            $order = $checkoutService->processCheckout($validated, $validated['items'], $userId);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pesanan Anda berhasil dibuat!',
                    'order_number' => $order->order_number,
                    'redirect_url' => route('order.show', $order->order_number),
                ]);
            }

            return redirect()->route('order.show', $order->order_number)->with('success', 'Pesanan Anda berhasil dibuat!');
        } catch (Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}
