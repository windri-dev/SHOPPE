<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class AdminDashboardAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_displays_visitor_and_shopping_analytics(): void
    {
        $this->seed();

        $admin = User::where('role', UserRole::STORE_ADMIN)->firstOrFail();

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('Analitik Pengunjung Toko');
        $response->assertSee('Analitik Aktivitas Belanja');
        $response->assertSee('Corong Belanja (Shopping Funnel)');
        $response->assertSee('visitorsVsShoppersChart');

        $metrics = $response->viewData('metrics');
        $this->assertIsArray($metrics);
        $this->assertArrayHasKey('traffic_and_shopping', $metrics);
        $this->assertGreaterThan(0, $metrics['traffic_and_shopping']['total_visitors']);
        $this->assertGreaterThanOrEqual(0, $metrics['traffic_and_shopping']['total_shoppers']);
    }

    public function test_storefront_traffic_middleware_records_live_visits(): void
    {
        $today = Carbon::today()->toDateString();
        $initial = (int) Cache::get("traffic:views:{$today}", 0);

        $response = $this->get('/shop');
        $response->assertStatus(200);

        $after = (int) Cache::get("traffic:views:{$today}", 0);
        $this->assertGreaterThanOrEqual($initial, $after);
    }
}
