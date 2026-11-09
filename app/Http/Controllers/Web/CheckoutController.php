<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Checkout\ProcessCheckoutRequest;
use App\Services\CheckoutService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
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
