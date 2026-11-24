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
use App\Models\StoreSetting;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
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
        if (app()->environment('staging', 'production') || request()->header('X-Forwarded-Proto') === 'https') {
            URL::forceScheme('https');
        }

        // Dynamic Rate Limiter per IP untuk Live Chat AI
        RateLimiter::for('livechat-ip', function (Request $request) {
            $limitPerMinute = (int) StoreSetting::get('livechat_rate_limit_per_minute', (string) env('LIVECHAT_RATE_LIMIT_PER_MINUTE', '10'));
            $clientIp = $request->ip() ?: '127.0.0.1';

            return Limit::perMinute(max(1, $limitPerMinute))
                ->by($clientIp)
                ->response(function (Request $request, array $headers) use ($limitPerMinute) {
                    return response()->json([
                        'success' => false,
                        'message' => "Mohon maaf, Anda mengirim pesan terlalu cepat (batas proteksi: {$limitPerMinute} pesan/menit). Demi kenyamanan bersama, silakan tunggu sejenak sebelum mengirim pesan baru.",
                        'retry_after' => $headers['Retry-After'] ?? 60,
                    ], 429, $headers);
                });
        });
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
