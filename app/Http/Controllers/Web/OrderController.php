<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\ConfirmPaymentRequest;
use App\Models\Order;
use App\Services\CurrencyService;
use App\Services\Payment\PaymentGatewayManager;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
                Log::channel('transactions')->info("Payment session initiated for order [{$order->order_number}]", [
                    'order_number' => $order->order_number,
                    'provider' => $paymentSession['provider'] ?? 'unknown',
                    'token_present' => ! empty($paymentSession['token']),
                ]);
            } catch (\Exception $e) {
                Log::channel('transactions')->warning("Payment session initiation failed for order [{$order->order_number}]: {$e->getMessage()}", [
                    'order_number' => $order->order_number,
                    'exception' => $e::class,
                ]);
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
            $channel = $request->input('channel', 'pga_simulator');
            $channelLabel = match ($channel) {
                'qris' => 'QRIS Instant (Simulasi Sandbox)',
                'virtual_account' => 'Virtual Account Bank (Simulasi Sandbox)',
                'credit_card' => 'Kartu Kredit / Debit (Simulasi Sandbox)',
                default => 'Simulasi Sandbox Gateway',
            };

            $existingNotes = $order->notes ? $order->notes."\n" : '';
            $order->update([
                'payment_status' => 'paid',
                'paid_at' => Carbon::now(),
                'status' => OrderStatus::PROCESSING,
                'payment_method' => $channel,
                'notes' => $existingNotes."[Pembayaran Lunas via {$channelLabel}]",
            ]);

            return back()->with('success', 'Pembayaran transaksi berhasil diselesaikan via '.$channelLabel.'! Pesanan Anda saat ini berstatus Lunas dan sedang dikemas oleh butik PUTRIJALVA.');
        }

        $transferAmount = (float) $validated['transfer_amount'];
        // Jika nominal diinput dalam USD kecil (< 1000) sedangkan tagihan dalam USD, konversi ke IDR untuk catatan
        if ($transferAmount < 1000 && (float) $order->grand_total < 1000) {
            $transferAmount = app(CurrencyService::class)->convert($transferAmount, 'IDR');
        }

        $proofPath = null;
        if ($request->hasFile('payment_proof')) {
            $proofPath = $request->file('payment_proof')->store('proofs', 'public');
        }

        $note = "Konfirmasi Transfer: Bank {$validated['bank_name']} a.n. {$validated['sender_name']} (Rp ".number_format($transferAmount, 0, ',', '.').')';
        if (! empty($validated['transfer_notes'])) {
            $note .= " - {$validated['transfer_notes']}";
        }
        if ($proofPath) {
            $note .= ' [Lampiran bukti struk transfer diunggah]';
        }

        $existingNotes = $order->notes ? $order->notes."\n" : '';

        $updateData = [
            'payment_status' => 'waiting_verification',
            'notes' => $existingNotes.$note,
        ];

        if ($proofPath) {
            $updateData['payment_proof'] = $proofPath;
        }

        $order->update($updateData);

        return back()->with('success', 'Bukti konfirmasi transfer dan struk Anda telah kami terima. Tim Finance PUTRIJALVA akan memverifikasi mutasi rekening Anda segera.');
    }
}
