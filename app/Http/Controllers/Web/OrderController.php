<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\ConfirmPaymentRequest;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function show(string $orderNumber): View
    {
        $order = Order::with('items.product')->where('order_number', $orderNumber)->firstOrFail();

        return view('pages.order-details', compact('order'));
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
