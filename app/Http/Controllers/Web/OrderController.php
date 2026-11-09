<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\ConfirmPaymentRequest;
use App\Models\Order;
use App\Services\Payment\PaymentGatewayManager;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(
        protected PaymentGatewayManager $paymentManager
    ) {}

    public function show(string $orderNumber): View
    {
        $order = Order::with('items.product', 'transactions')->where('order_number', $orderNumber)->firstOrFail();

        // Inisialisasi token PGA jika pesanan belum lunas dan belum memiliki reference
        $paymentSession = null;
        if (! $order->isPaid()) {
            try {
                $paymentSession = $this->paymentManager->initiatePayment($order);
            } catch (\Exception $e) {
                // Abaikan jika offline / dev fallback
            }
        }

        return view('pages.order-details', compact('order', 'paymentSession'));
    }

    /**
     * Endpoint API status pesanan untuk polling frontend saat modal bayar aktif.
     */
    public function checkStatus(string $orderNumber): JsonResponse
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        return response()->json([
            'order_number' => $order->order_number,
            'payment_status' => $order->payment_status,
            'status' => $order->status->value ?? (string) $order->status,
            'is_paid' => $order->isPaid(),
            'paid_at' => $order->paid_at?->toIso8601String(),
        ]);
    }

    /**
     * Endpoint inisialisasi / generate ulang sesi pembayaran PGA.
     */
    public function initiatePgaPayment(Request $request, string $orderNumber): JsonResponse
    {
        $order = Order::with('items')->where('order_number', $orderNumber)->firstOrFail();

        $provider = $request->input('provider');
        $session = $this->paymentManager->initiatePayment($order, $provider);

        return response()->json([
            'success' => true,
            'session' => $session,
        ]);
    }

    public function confirmPayment(ConfirmPaymentRequest $request, string $orderNumber): RedirectResponse
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();
        $validated = $request->validated();

        $actionType = $validated['action_type'] ?? 'manual_transfer';

        if ($actionType === 'instant_simulate') {
            $order->update([
                'payment_status' => 'paid',
                'paid_at' => Carbon::now(),
                'status' => OrderStatus::PROCESSING,
            ]);

            return back()->with('success', 'Pembayaran berhasil dikonfirmasi! Pesanan Anda saat ini sedang disiapkan oleh tim perajin PUTRI.J.');
        }

        $note = "Konfirmasi Transfer: Bank {$validated['bank_name']} a.n. {$validated['sender_name']} (Rp ".number_format((float) $validated['transfer_amount'], 0, ',', '.').')';
        if (! empty($validated['transfer_notes'])) {
            $note .= " - {$validated['transfer_notes']}";
        }

        $existingNotes = $order->notes ? $order->notes."\n" : '';

        $order->update([
            'payment_status' => 'waiting_verification',
            'notes' => $existingNotes.$note,
        ]);

        return back()->with('success', 'Bukti konfirmasi transfer Anda telah kami terima. Tim Finance PUTRI.J akan memverifikasi mutasi rekening dalam 15-30 menit.');
    }
}
