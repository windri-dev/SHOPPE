<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class StorefrontAndAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $category = Category::create([
            'name' => 'Earrings',
            'slug' => 'earrings',
            'description' => 'Drop and hoop earrings.',
        ]);

        Product::create([
            'category_id' => $category->id,
            'sku' => 'SKU-0012',
            'name' => 'Lira Earrings',
            'slug' => 'lira-earrings',
            'price' => 20.00,
            'stock' => 24,
            'is_featured' => true,
            'is_active' => true,
            'sales_count' => 48,
            'description' => 'Cast in 18k gold-dipped brass.',
            'images' => ['images/product-1.png'],
        ]);

        User::create([
            'name' => 'SHOPPE Store Manager',
            'email' => 'admin@shoppe.com',
            'password' => Hash::make('admin123'),
            'role' => UserRole::STORE_ADMIN,
        ]);

        User::create([
            'name' => 'Clara Aurelia',
            'email' => 'buyer@shoppe.com',
            'password' => Hash::make('buyer123'),
            'role' => UserRole::CUSTOMER,
        ]);
    }

    /**
     * Test storefront home page renders successfully.
     */
    public function test_storefront_home_page_is_accessible(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('SHOPPE');
        $response->assertSee('Shop The Latest');
    }

    /**
     * Test shop catalog page renders products.
     */
    public function test_shop_catalog_page_is_accessible(): void
    {
        $response = $this->get('/shop');
        $response->assertStatus(200);
        $response->assertSee('Lira Earrings');
    }

    /**
     * Test admin dashboard blocks unauthorized guest and redirects to admin login.
     */
    public function test_admin_dashboard_redirects_unauthenticated_visitor(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/admin/login');
    }

    /**
     * Test admin dashboard blocks regular customers with 403 Forbidden.
     */
    public function test_admin_dashboard_blocks_regular_customer(): void
    {
        $customer = User::where('role', UserRole::CUSTOMER)->first();
        $this->assertNotNull($customer);

        $response = $this->actingAs($customer)->get('/admin');
        $response->assertStatus(403);
    }

    /**
     * Test admin dashboard allows store admin.
     */
    public function test_admin_dashboard_allows_store_admin(): void
    {
        $admin = User::where('role', UserRole::STORE_ADMIN)->first();
        $this->assertNotNull($admin);

        $response = $this->actingAs($admin)->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('Master Store Overview');
        $response->assertSee('Total Omzet');
    }
}
