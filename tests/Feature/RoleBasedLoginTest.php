<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class RoleBasedLoginTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $storeAdminUser;
    protected User $regularUser;
    protected User $customerUser;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. User dengan role explicit 'admin'
        $this->adminUser = User::create([
            'name' => 'System Admin',
            'email' => 'admin.system@shoppe.com',
            'password' => Hash::make('password123'),
            'role' => UserRole::ADMIN,
        ]);

        // 2. User dengan role 'store_admin'
        $this->storeAdminUser = User::create([
            'name' => 'Store Administrator',
            'email' => 'store.admin@shoppe.com',
            'password' => Hash::make('password123'),
            'role' => UserRole::STORE_ADMIN,
        ]);

        // 3. User dengan role explicit 'user'
        $this->regularUser = User::create([
            'name' => 'Regular Member',
            'email' => 'member@example.com',
            'password' => Hash::make('password123'),
            'role' => UserRole::USER,
        ]);

        // 4. User dengan role 'customer'
        $this->customerUser = User::create([
            'name' => 'Online Customer',
            'email' => 'customer@example.com',
            'password' => Hash::make('password123'),
            'role' => UserRole::CUSTOMER,
        ]);
    }

    /**
     * Test admin dengan role 'admin' diarahkan langsung ke Dashboard Admin setelah login.
     */
    public function test_admin_user_redirects_to_admin_dashboard_after_login(): void
    {
        $response = $this->post(route('account.login'), [
            'email' => 'admin.system@shoppe.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($this->adminUser);
        $response->assertRedirect(route('admin.dashboard'));
    }

    /**
     * Test admin dengan role 'store_admin' diarahkan langsung ke Dashboard Admin setelah login.
     */
    public function test_store_admin_redirects_to_admin_dashboard_after_login(): void
    {
        $response = $this->post('/login', [
            'email' => 'store.admin@shoppe.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($this->storeAdminUser);
        $response->assertRedirect(route('admin.dashboard'));
    }

    /**
     * Test pengguna dengan role 'user' diarahkan langsung ke Halaman User setelah login.
     */
    public function test_user_role_redirects_to_user_page_after_login(): void
    {
        $response = $this->post(route('account.login'), [
            'email' => 'member@example.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($this->regularUser);
        $response->assertRedirect(route('account.index'));
    }

    /**
     * Test pengguna dengan role 'customer' diarahkan langsung ke Halaman User setelah login.
     */
    public function test_customer_role_redirects_to_user_page_after_login(): void
    {
        $response = $this->post('/login', [
            'email' => 'customer@example.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($this->customerUser);
        $response->assertRedirect(route('account.index'));
    }

    /**
     * Test admin login melalui admin portal gate diarahkan ke Dashboard Admin.
     */
    public function test_admin_login_at_admin_gate_redirects_to_dashboard(): void
    {
        $response = $this->post(route('admin.login.submit'), [
            'email' => 'admin.system@shoppe.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($this->adminUser);
        $response->assertRedirect(route('admin.dashboard'));
    }

    /**
     * Test pengguna biasa login melalui gate admin divalidasi dan dialihkan ke Halaman User.
     */
    public function test_regular_user_at_admin_gate_redirects_to_user_page(): void
    {
        $response = $this->post(route('admin.login.submit'), [
            'email' => 'member@example.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($this->regularUser);
        $response->assertRedirect(route('account.index'));
    }

    /**
     * Test admin yang sudah login mengakses /login atau /account dialihkan ke Dashboard Admin.
     */
    public function test_authenticated_admin_visiting_account_redirects_to_admin_dashboard(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('account.index'));
        $response->assertRedirect(route('admin.dashboard'));
    }

    /**
     * Test pengguna biasa tidak dapat mengakses Dashboard Admin dan diblokir dengan 403 Forbidden.
     */
    public function test_regular_user_is_forbidden_from_admin_dashboard(): void
    {
        $response = $this->actingAs($this->regularUser)->get(route('admin.dashboard'));
        $response->assertStatus(403);
    }

    /**
     * Test kegagalan login dengan kredensial salah mengembalikan error validasi.
     */
    public function test_invalid_login_credentials_returns_error(): void
    {
        $response = $this->post(route('account.login'), [
            'email' => 'admin.system@shoppe.com',
            'password' => 'wrongpassword',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }
}

