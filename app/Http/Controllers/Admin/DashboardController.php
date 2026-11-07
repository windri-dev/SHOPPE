<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminDashboardService;
use App\Models\Order;
use App\Models\Product;
use App\Models\Inquiry;
use App\Enums\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(AdminDashboardService $dashboardService): View
    {
        $metrics = $dashboardService->getMetrics();
        $products = Product::with('category')->latest()->get();
        $orders = Order::with('items')->latest()->paginate(10);
        $inquiries = Inquiry::with('messages')->latest()->get();

        return view('admin.dashboard', compact('metrics', 'products', 'orders', 'inquiries'));
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        $order->update(['status' => $request->status]);

        return back()->with('success', "Status pesanan {$order->order_number} berhasil diperbarui.");
    }

    public function toggleProductStock(Product $product)
    {
        $newStock = $product->stock > 0 ? 0 : 15;
        $badge = $newStock === 0 ? 'Sold out' : null;
        $product->update([
            'stock' => $newStock,
            'badge' => $badge,
        ]);

        return back()->with('success', "Status stok produk {$product->name} berhasil diubah.");
    }

    public function replyInquiry(Request $request, Inquiry $inquiry)
    {
        $request->validate(['message' => 'required|string']);

        $inquiry->messages()->create([
            'sender_type' => 'admin',
            'message' => $request->message,
        ]);

        $inquiry->update(['status' => 'responded']);

        return back()->with('success', "Balasan terkirim ke tiket {$inquiry->ticket_number}.");
    }
}
