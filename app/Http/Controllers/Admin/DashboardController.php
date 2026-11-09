<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminDashboardService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected AdminDashboardService $dashboardService
    ) {}

    public function index(): View
    {
        $metrics = $this->dashboardService->getMetrics();

        return view('admin.dashboard', [
            'metrics' => $metrics,
            'products' => $metrics['recent_products'],
            'orders' => $metrics['recent_orders'],
            'inquiries' => $metrics['recent_inquiries'],
        ]);
    }
}
