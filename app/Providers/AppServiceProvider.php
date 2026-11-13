<?php

declare(strict_types=1);

namespace App\Providers;

use App\Enums\InquiryStatus;
use App\Enums\OrderStatus;
use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Inquiry;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View as ViewInstance;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.admin', function (ViewInstance $view): void {
            $view->with('sidebarMetrics', [
                'pending_orders' => Order::query()->where('status', OrderStatus::PENDING)->count(),
                'low_stock_products' => Product::query()->where('stock', '<=', 3)->count(),
                'total_products' => Product::query()->count(),
                'categories_count' => Category::query()->count(),
                'blogs_count' => BlogPost::query()->count(),
                'unread_inquiries' => Inquiry::query()->where('status', InquiryStatus::UNREAD)->count(),
            ]);
        });
    }
}
