<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\InquiryStatus;
use App\Enums\OrderStatus;
use App\Models\Inquiry;
use App\Models\Order;
use App\Models\Product;

class AdminDashboardService
{
    /**
     * Mengambil ringkasan metrik KPI dan data terbaru untuk Dashboard Admin.
     *
     * @return array<string, mixed>
     */
    public function getMetrics(): array
    {
        $totalRevenue = (float) Order::where('status', '!=', OrderStatus::CANCELLED)->sum('grand_total');
        $totalOrders = Order::count();
        $activeProducts = Product::where('is_active', true)->count();
        $pendingInquiries = Inquiry::where('status', InquiryStatus::UNREAD)->count();

        $recentOrders = Order::with('items')->latest()->paginate(8);
        $recentProducts = Product::with('category')->latest()->take(8)->get();
        $lowStockProducts = Product::where('stock', '<=', 5)->get();
        $recentInquiries = Inquiry::with('messages')->latest()->take(5)->get();

        return [
            'total_revenue' => $totalRevenue,
            'total_orders' => $totalOrders,
            'active_products' => $activeProducts,
            'pending_inquiries' => $pendingInquiries,
            'recent_orders' => $recentOrders,
            'recent_products' => $recentProducts,
            'low_stock_products' => $lowStockProducts,
            'recent_inquiries' => $recentInquiries,
        ];
    }
}
