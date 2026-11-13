<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WishlistFeatureTest extends TestCase
{
    use RefreshDatabase;

    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $category = Category::create([
            'name' => 'Dresses',
            'slug' => 'dresses',
            'description' => 'Midi and maxi dresses.',
        ]);

        $this->product = Product::create([
            'category_id' => $category->id,
            'sku' => 'SKU-WISH-1',
            'name' => 'Aurelia Silk Wrap Dress',
            'slug' => 'aurelia-silk-wrap-dress',
            'price' => 65.00,
            'stock' => 12,
            'is_featured' => true,
            'is_active' => true,
            'sales_count' => 10,
            'description' => 'Eksklusif silk wrap dress.',
            'images' => ['images/product-1.png'],
        ]);
    }

    public function test_guest_can_view_empty_wishlist_page(): void
    {
        $response = $this->get(route('wishlist.index'));

        $response->assertStatus(200);
        $response->assertSee('Daftar Keinginan');
        $response->assertSee('Daftar Keinginan Anda Masih Kosong');
    }

    public function test_guest_can_toggle_product_in_wishlist_via_session(): void
    {
        // Add to wishlist
        $response = $this->postJson(route('wishlist.toggle'), [
            'product_id' => $this->product->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'in_wishlist' => true,
            'count' => 1,
        ]);

        $this->assertEquals([$this->product->id], session('wishlist'));

        // View wishlist page as guest
        $pageResponse = $this->get(route('wishlist.index'));
        $pageResponse->assertStatus(200);
        $pageResponse->assertSee('Aurelia Silk Wrap Dress');

        // Toggle again to remove
        $removeResponse = $this->postJson(route('wishlist.toggle'), [
            'product_id' => $this->product->id,
        ]);

        $removeResponse->assertStatus(200);
        $removeResponse->assertJson([
            'success' => true,
            'in_wishlist' => false,
            'count' => 0,
        ]);

        $this->assertEmpty(session('wishlist'));
    }

    public function test_authenticated_user_can_toggle_wishlist_in_database(): void
    {
        $user = User::factory()->create();

        // Add to wishlist
        $response = $this->actingAs($user)->postJson(route('wishlist.toggle'), [
            'product_id' => $this->product->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'in_wishlist' => true,
            'count' => 1,
        ]);

        $this->assertDatabaseHas('wishlists', [
            'user_id' => $user->id,
            'product_id' => $this->product->id,
        ]);

        // Toggle again to remove
        $removeResponse = $this->actingAs($user)->postJson(route('wishlist.toggle'), [
            'product_id' => $this->product->id,
        ]);

        $removeResponse->assertStatus(200);
        $removeResponse->assertJson([
            'success' => true,
            'in_wishlist' => false,
            'count' => 0,
        ]);

        $this->assertDatabaseMissing('wishlists', [
            'user_id' => $user->id,
            'product_id' => $this->product->id,
        ]);
    }

    public function test_guest_wishlist_is_synced_to_database_upon_viewing_after_login(): void
    {
        $user = User::factory()->create();

        // Guest saves product in session
        $this->withSession(['wishlist' => [$this->product->id]]);

        // User logs in and visits wishlist page
        $response = $this->actingAs($user)->get(route('wishlist.index'));

        $response->assertStatus(200);
        $response->assertSee('Aurelia Silk Wrap Dress');

        // Database now has the item
        $this->assertDatabaseHas('wishlists', [
            'user_id' => $user->id,
            'product_id' => $this->product->id,
        ]);

        // Session is cleared
        $this->assertNull(session('wishlist'));
    }

    public function test_user_can_remove_product_from_wishlist_via_delete_route(): void
    {
        $user = User::factory()->create();
        Wishlist::create([
            'user_id' => $user->id,
            'product_id' => $this->product->id,
        ]);

        $response = $this->actingAs($user)->delete(route('wishlist.destroy', $this->product->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('wishlists', [
            'user_id' => $user->id,
            'product_id' => $this->product->id,
        ]);
    }

    public function test_guest_wishlist_is_synced_to_database_upon_user_login(): void
    {
        $user = User::factory()->create([
            'email' => 'buyer@putrijalva.id',
            'password' => bcrypt('Secret123!'),
        ]);

        $this->withSession(['wishlist' => [$this->product->id]]);

        $response = $this->post(route('account.login'), [
            'email' => 'buyer@putrijalva.id',
            'password' => 'Secret123!',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('wishlists', [
            'user_id' => $user->id,
            'product_id' => $this->product->id,
        ]);
        $this->assertNull(session('wishlist'));
    }

    public function test_guest_wishlist_is_synced_to_database_upon_user_register(): void
    {
        $this->withSession(['wishlist' => [$this->product->id]]);

        $response = $this->post(route('account.register'), [
            'name' => 'New Customer',
            'email' => 'newbuyer@putrijalva.id',
            'password' => 'Secret123!',
            'password_confirmation' => 'Secret123!',
            'address' => 'Jl. Kebon Jeruk No. 5',
        ]);

        $response->assertRedirect();
        $user = User::where('email', 'newbuyer@putrijalva.id')->firstOrFail();

        $this->assertDatabaseHas('wishlists', [
            'user_id' => $user->id,
            'product_id' => $this->product->id,
        ]);
        $this->assertNull(session('wishlist'));
    }
}
