<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateOrderPaymentRequest;
use App\Http\Requests\Admin\UpdateOrderStatusRequest;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderAdminController extends Controller
{
    public function index(Request $request): View
    {
        $query = Order::with('items')->latest();

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status') && $request->payment_status !== 'all') {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%")
                    ->orWhere('tracking_number', 'like', "%{$search}%");
            });
        }

        $orders = $query->paginate(12)->withQueryString();

        $statusCounts = [
            'all' => Order::count(),
            'pending' => Order::where('status', OrderStatus::PENDING)->count(),
            'processing' => Order::where('status', OrderStatus::PROCESSING)->count(),
            'shipped' => Order::where('status', OrderStatus::SHIPPED)->count(),
            'delivered' => Order::where('status', OrderStatus::DELIVERED)->count(),
            'cancelled' => Order::where('status', OrderStatus::CANCELLED)->count(),
        ];

        $paymentCounts = [
            'unpaid' => Order::where('payment_status', 'unpaid')->count(),
            'waiting' => Order::where('payment_status', 'waiting_verification')->count(),
            'paid' => Order::where('payment_status', 'paid')->count(),
            'refunded' => Order::where('payment_status', 'refunded')->count(),
        ];

        return view('admin.orders.index', compact('orders', 'statusCounts', 'paymentCounts'));
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): RedirectResponse
    {
        $status = OrderStatus::from($request->validated('status'));
        $order->update(['status' => $status]);

        return back()->with('success', "Status pesanan {$order->order_number} berhasil diubah menjadi {$status->label()}.");
    }

    public function updatePayment(UpdateOrderPaymentRequest $request, Order $order): RedirectResponse
    {
        $validated = $request->validated();

        $updates = [
            'payment_status' => $validated['payment_status'],
        ];

        if (! empty($validated['tracking_number'])) {
            $updates['tracking_number'] = $validated['tracking_number'];
        }

        if (! empty($validated['notes'])) {
            $updates['notes'] = $validated['notes'];
        }

        if ($validated['payment_status'] === 'paid') {
            if (! $order->paid_at) {
                $updates['paid_at'] = Carbon::now();
            }

            if ($order->status === OrderStatus::PENDING) {
                $updates['status'] = OrderStatus::PROCESSING;
            }
        } elseif ($validated['payment_status'] === 'refunded') {
            $updates['status'] = OrderStatus::CANCELLED;
        }

        $currentStatus = $updates['status'] ?? $order->status;
        if (! empty($validated['tracking_number']) && in_array($currentStatus, [OrderStatus::PENDING, OrderStatus::PROCESSING], true)) {
            $updates['status'] = OrderStatus::SHIPPED;
        }

        $order->update($updates);

        return back()->with('success', "Status pembayaran & pengiriman pesanan {$order->order_number} berhasil diperbarui.");
    }
}
