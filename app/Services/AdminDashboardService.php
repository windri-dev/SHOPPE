<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\Inquiry;
use App\Enums\OrderStatus;

class AdminDashboardService
{
    public function getMetrics(): array
    {
        $totalRevenue = Order::where('status', '!=', OrderStatus::CANCELLED)->sum('grand_total');
        $totalOrders = Order::count();
        $activeProducts = Product::where('is_active', true)->count();
        $pendingInquiries = Inquiry::where('status', 'unread')->count();

        $recentOrders = Order::with('items')->latest()->take(5)->get();
        $lowStockProducts = Product::where('stock', '<=', 5)->get();
        $recentInquiries = Inquiry::with('messages')->latest()->take(5)->get();

        return [
            'total_revenue' => $totalRevenue,
            'total_orders' => $totalOrders,
            'active_products' => $activeProducts,
            'pending_inquiries' => $pendingInquiries,
            'recent_orders' => $recentOrders,
            'low_stock_products' => $lowStockProducts,
            'recent_inquiries' => $recentInquiries,
        ];
    }
}
