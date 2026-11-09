<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\InquiryStatus;
use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Models\AiKnowledgeEntry;
use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Inquiry;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StorefrontAndAdminTest extends TestCase
{
    use RefreshDatabase;

    private Product $product;

    private BlogPost $blogPost;

    protected function setUp(): void
    {
        parent::setUp();

        $category = Category::create([
            'name' => 'Earrings',
            'slug' => 'earrings',
            'description' => 'Drop and hoop earrings.',
        ]);

        $this->product = Product::create([
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

        $this->blogPost = BlogPost::create([
            'title' => 'Top Trends From Spring',
            'slug' => 'top-trends-from-spring',
            'category' => 'Fashion',
            'author' => 'ANNY JOHNSON',
            'image' => 'images/blog-1.png',
            'excerpt' => 'Minimalist gold silhouettes and sculptural forms.',
            'content' => 'Spring brings a renewed appreciation for effortless layering.',
            'published_at' => now(),
        ]);

        User::create([
            'name' => 'PUTRI.J Store Manager',
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

    public function test_storefront_home_page_is_accessible(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('PUTRI.J');
        $response->assertSee('Shop The Latest');
    }

    public function test_shop_catalog_and_product_detail_pages_are_accessible(): void
    {
        $catalogResponse = $this->get('/shop');
        $catalogResponse->assertStatus(200);
        $catalogResponse->assertSee('Lira Earrings');

        $detailResponse = $this->get('/shop/'.$this->product->slug);
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('Lira Earrings');
        $detailResponse->assertSee('SKU-0012');
    }

    public function test_blog_index_and_single_article_pages_are_accessible(): void
    {
        $indexResponse = $this->get('/blog');
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Top Trends From Spring');

        $showResponse = $this->get('/blog/'.$this->blogPost->slug);
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Top Trends From Spring');
        $showResponse->assertSee('ANNY JOHNSON');
    }

    public function test_guest_cannot_access_checkout_and_is_redirected_to_login(): void
    {
        $response = $this->get('/checkout');
        $response->assertRedirect(route('login'));
        $response->assertSessionHas('info');

        $jsonResponse = $this->postJson('/checkout', [
            'first_name' => 'Tamu',
            'last_name' => 'Anonim',
            'email' => 'guest@example.com',
            'phone' => '08123456789',
            'country' => 'Indonesia',
            'street_address' => 'Jl. Test No. 1',
            'city' => 'Jakarta',
            'postcode' => '12345',
            'payment_method' => 'bank_transfer',
            'items' => [
                ['id' => $this->product->id, 'quantity' => 1],
            ],
        ]);
        $jsonResponse->assertStatus(401);

        // Intended redirect verification: after login, customer is redirected back to /checkout
        $loginResponse = $this->post(route('account.login'), [
            'email' => 'buyer@shoppe.com',
            'password' => 'buyer123',
        ]);
        $loginResponse->assertRedirect(route('checkout.index'));

        // Logout and verify new registration also redirects back to /checkout
        $this->post(route('account.logout'));
        $this->get('/checkout'); // set intended URL again
        $registerResponse = $this->post(route('account.register'), [
            'name' => 'Dewi Sartika',
            'email' => 'dewi@example.com',
            'password' => 'dewi12345',
            'password_confirmation' => 'dewi12345',
        ]);
        $registerResponse->assertRedirect(route('checkout.index'));
    }

    public function test_cart_and_checkout_flow_creates_order_and_decrements_stock(): void
    {
        $this->get('/cart')->assertStatus(200);

        /** @var User $customer */
        $customer = User::where('email', 'buyer@shoppe.com')->first();

        $this->actingAs($customer)->get('/checkout')->assertStatus(200);

        $checkoutResponse = $this->actingAs($customer)->post('/checkout', [
            'first_name' => 'Clara',
            'last_name' => 'Aurelia',
            'email' => 'buyer@shoppe.com',
            'phone' => '+628123456789',
            'country' => 'Indonesia',
            'street_address' => 'Jl. Senopati No. 45',
            'city' => 'Jakarta',
            'postcode' => '12190',
            'payment_method' => 'bank_transfer',
            'items' => [
                [
                    'id' => $this->product->id,
                    'quantity' => 2,
                ],
            ],
        ]);

        $order = Order::first();
        $this->assertNotNull($order);
        $checkoutResponse->assertRedirect(route('order.show', $order->order_number));

        $this->assertEquals(22, $this->product->fresh()->stock);
        $this->assertEquals(40.00, (float) $order->grand_total);

        $orderPage = $this->get(route('order.show', $order->order_number));
        $orderPage->assertStatus(200);
        $orderPage->assertSee($order->order_number);
        $orderPage->assertSee('Lira Earrings');
    }

    public function test_contact_inquiry_submission_stores_inquiry_and_initial_message(): void
    {
        $this->get('/contact')->assertStatus(200);

        $response = $this->post('/contact', [
            'name' => 'Clara Aurelia',
            'email' => 'buyer@shoppe.com',
            'phone' => '+628123456789',
            'subject' => 'Custom Engraving Request',
            'message' => 'Can I engrave initials on the Lira Earrings?',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $inquiry = Inquiry::with('messages')->first();
        $this->assertNotNull($inquiry);
        $this->assertEquals(InquiryStatus::UNREAD, $inquiry->status);
        $this->assertCount(1, $inquiry->messages);
    }

    public function test_admin_dashboard_redirects_unauthenticated_visitor(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_dashboard_blocks_regular_customer(): void
    {
        $customer = User::where('role', UserRole::CUSTOMER)->firstOrFail();

        $response = $this->actingAs($customer)->get('/admin');
        $response->assertStatus(403);
    }

    public function test_admin_can_view_dashboard_update_order_status_and_reply_inquiry(): void
    {
        $admin = User::where('role', UserRole::STORE_ADMIN)->firstOrFail();

        $response = $this->actingAs($admin)->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('Master Store Overview');
        $response->assertSee('Total Omzet');

        // Create an order and update its status as admin
        $order = Order::create([
            'order_number' => 'SHP-TEST01',
            'status' => OrderStatus::PENDING,
            'subtotal' => 20.00,
            'shipping_cost' => 0,
            'discount_amount' => 0,
            'grand_total' => 20.00,
            'customer_name' => 'Clara Aurelia',
            'customer_email' => 'buyer@shoppe.com',
            'shipping_address' => ['city' => 'Jakarta'],
            'payment_method' => 'bank_transfer',
        ]);

        $statusResponse = $this->actingAs($admin)->post(route('admin.orders.status', $order), [
            'status' => OrderStatus::PROCESSING->value,
        ]);
        $statusResponse->assertRedirect();
        $this->assertEquals(OrderStatus::PROCESSING, $order->fresh()->status);

        // Create an inquiry and reply as admin
        $inquiry = Inquiry::create([
            'ticket_number' => 'INQ-TEST01',
            'name' => 'Clara Aurelia',
            'email' => 'buyer@shoppe.com',
            'subject' => 'Order Question',
            'status' => InquiryStatus::UNREAD,
        ]);

        $replyResponse = $this->actingAs($admin)->post(route('admin.inquiries.reply', $inquiry), [
            'message' => 'Your order is now being processed by our artisan team.',
        ]);
        $replyResponse->assertRedirect();
        $this->assertEquals(InquiryStatus::RESPONDED, $inquiry->fresh()->status);
        $this->assertCount(1, $inquiry->messages);
    }

    public function test_admin_can_access_dedicated_livechat_menu(): void
    {
        $admin = User::where('role', UserRole::STORE_ADMIN)->firstOrFail();

        $response = $this->actingAs($admin)->get(route('admin.livechat'));
        $response->assertStatus(200);
        $response->assertSee('Pusat Interaksi Live Chat &amp; AI Concierge Hybrid', false);
        $response->assertSee('Inbox Obrolan');
    }

    public function test_admin_can_reply_and_update_inquiry_status_via_ajax(): void
    {
        $admin = User::where('role', UserRole::STORE_ADMIN)->firstOrFail();

        $inquiry = Inquiry::create([
            'ticket_number' => 'TCK-AJAX-01',
            'name' => 'Dimas Satria',
            'email' => 'dimas@example.com',
            'subject' => 'Tukar Size',
            'status' => InquiryStatus::UNREAD,
        ]);

        // 1. Reply via AJAX
        $replyResponse = $this->actingAs($admin)->postJson(route('admin.inquiries.reply', $inquiry), [
            'message' => 'Bisa Kak Dimas, akan kami siapkan size pengganti.',
        ]);

        $replyResponse->assertStatus(200);
        $replyResponse->assertJson([
            'success' => true,
            'data' => [
                'message' => 'Bisa Kak Dimas, akan kami siapkan size pengganti.',
                'inquiry_status' => 'responded',
            ],
        ]);

        $this->assertEquals(InquiryStatus::RESPONDED, $inquiry->fresh()->status);

        // 2. Update status to resolved via AJAX
        $statusResponse = $this->actingAs($admin)->postJson(route('admin.inquiries.status', $inquiry), [
            'status' => 'resolved',
        ]);

        $statusResponse->assertStatus(200);
        $statusResponse->assertJson([
            'success' => true,
            'data' => [
                'status' => 'resolved',
            ],
        ]);

        $this->assertEquals(InquiryStatus::RESOLVED, $inquiry->fresh()->status);
    }

    public function test_admin_can_access_orders_management_menu(): void
    {
        $admin = User::where('role', UserRole::STORE_ADMIN)->firstOrFail();

        $response = $this->actingAs($admin)->get(route('admin.orders.index'));
        $response->assertStatus(200);
        $response->assertSee('Daftar Semua Pesanan Masuk');
        $response->assertSee('Menunggu');
        $response->assertSee('Diproses');
    }

    public function test_admin_can_update_order_status(): void
    {
        $admin = User::where('role', UserRole::STORE_ADMIN)->firstOrFail();
        $buyer = User::where('role', UserRole::CUSTOMER)->firstOrFail();

        $order = Order::create([
            'user_id' => $buyer->id,
            'order_number' => 'ORD-TEST-99',
            'status' => OrderStatus::PENDING,
            'subtotal' => 50.00,
            'shipping_cost' => 5.00,
            'grand_total' => 55.00,
            'customer_name' => $buyer->name,
            'customer_email' => $buyer->email,
            'customer_phone' => '08123456789',
            'shipping_address' => ['address' => 'Jl. Test 123'],
            'payment_method' => 'bank_transfer',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.orders.status', $order), [
            'status' => 'processing',
        ]);

        $response->assertRedirect();
        $this->assertEquals(OrderStatus::PROCESSING, $order->fresh()->status);
    }

    public function test_admin_can_access_products_inventory_menu(): void
    {
        $admin = User::where('role', UserRole::STORE_ADMIN)->firstOrFail();

        $response = $this->actingAs($admin)->get(route('admin.products.index'));
        $response->assertStatus(200);
        $response->assertSee('Katalog &amp; Manajemen Stok Produk', false);
        $response->assertSee('Lira Earrings');
    }

    public function test_admin_can_toggle_product_stock(): void
    {
        $admin = User::where('role', UserRole::STORE_ADMIN)->firstOrFail();

        // Product has stock 24 -> toggle should set it to 0
        $response = $this->actingAs($admin)->patch(route('admin.products.toggle_stock', $this->product));
        $response->assertRedirect();
        $this->assertEquals(0, $this->product->fresh()->stock);
        $this->assertEquals('Sold out', $this->product->fresh()->badge);

        // Toggle again -> stock should become 15
        $this->actingAs($admin)->patch(route('admin.products.toggle_stock', $this->product));
        $this->assertEquals(15, $this->product->fresh()->stock);
        $this->assertNull($this->product->fresh()->badge);
    }

    public function test_admin_can_update_order_payment_and_advance_status(): void
    {
        $admin = User::where('role', UserRole::STORE_ADMIN)->firstOrFail();
        $buyer = User::where('role', UserRole::CUSTOMER)->firstOrFail();

        $order = Order::create([
            'user_id' => $buyer->id,
            'order_number' => 'ORD-PAY-01',
            'status' => OrderStatus::PENDING,
            'payment_status' => 'unpaid',
            'subtotal' => 100.00,
            'shipping_cost' => 0.00,
            'grand_total' => 100.00,
            'customer_name' => $buyer->name,
            'customer_email' => $buyer->email,
            'customer_phone' => '08123456789',
            'shipping_address' => ['street' => 'Jl. Sudirman No. 1'],
            'payment_method' => 'bank_transfer',
        ]);

        // Admin verifies payment to paid -> should set paid_at and advance status from PENDING to PROCESSING
        $response = $this->actingAs($admin)->post(route('admin.orders.payment', $order), [
            'payment_status' => 'paid',
            'tracking_number' => 'JNE-99210291',
        ]);

        $response->assertRedirect();
        $fresh = $order->fresh();
        $this->assertEquals('paid', $fresh->payment_status);
        $this->assertNotNull($fresh->paid_at);
        $this->assertEquals(OrderStatus::SHIPPED, $fresh->status); // Had tracking number -> SHIPPED
    }

    public function test_customer_can_simulate_instant_payment_on_order_details(): void
    {
        $buyer = User::where('role', UserRole::CUSTOMER)->firstOrFail();

        $order = Order::create([
            'user_id' => $buyer->id,
            'order_number' => 'ORD-SIM-01',
            'status' => OrderStatus::PENDING,
            'payment_status' => 'unpaid',
            'subtotal' => 75.00,
            'shipping_cost' => 0.00,
            'grand_total' => 75.00,
            'customer_name' => $buyer->name,
            'customer_email' => $buyer->email,
            'customer_phone' => '08123456789',
            'shipping_address' => ['street' => 'Jl. Senopati'],
            'payment_method' => 'bank_transfer',
        ]);

        $response = $this->actingAs($buyer)->post(route('order.confirm_payment', $order->order_number), [
            'action_type' => 'instant_simulate',
        ]);

        $response->assertRedirect();
        $fresh = $order->fresh();
        $this->assertEquals('paid', $fresh->payment_status);
        $this->assertEquals(OrderStatus::PROCESSING, $fresh->status);
    }

    public function test_customer_can_confirm_manual_transfer_details(): void
    {
        $buyer = User::where('role', UserRole::CUSTOMER)->firstOrFail();

        $order = Order::create([
            'user_id' => $buyer->id,
            'order_number' => 'ORD-MAN-01',
            'status' => OrderStatus::PENDING,
            'payment_status' => 'unpaid',
            'subtotal' => 50.00,
            'shipping_cost' => 0.00,
            'grand_total' => 50.00,
            'customer_name' => $buyer->name,
            'customer_email' => $buyer->email,
            'customer_phone' => '08123456789',
            'shipping_address' => ['street' => 'Jl. Gatot Subroto'],
            'payment_method' => 'bank_transfer',
        ]);

        $response = $this->actingAs($buyer)->post(route('order.confirm_payment', $order->order_number), [
            'action_type' => 'manual_transfer',
            'bank_name' => 'BCA',
            'sender_name' => 'Clara Aurelia',
            'transfer_amount' => 50.00,
            'transfer_notes' => 'Ref: TRX12345',
        ]);

        $response->assertRedirect();
        $fresh = $order->fresh();
        $this->assertEquals('waiting_verification', $fresh->payment_status);
        $this->assertStringContainsString('Konfirmasi Transfer: Bank BCA', (string) $fresh->notes);
    }

    public function test_admin_can_create_new_product(): void
    {
        $admin = User::where('role', UserRole::STORE_ADMIN)->firstOrFail();
        $category = Category::firstOrFail();

        $response = $this->actingAs($admin)->post(route('admin.products.store'), [
            'name' => 'Solitaire Diamond Ring',
            'category_id' => $category->id,
            'sku' => 'SKU-SOL-99',
            'price' => 120.00,
            'stock' => 10,
            'badge' => 'Exclusive',
            'description' => 'Solid gold with solitaire diamond.',
        ]);

        $response->assertRedirect();
        $newProduct = Product::where('sku', 'SKU-SOL-99')->first();
        $this->assertNotNull($newProduct);
        $this->assertEquals('Solitaire Diamond Ring', $newProduct->name);
        $this->assertEquals(10, $newProduct->stock);
    }

    public function test_admin_can_adjust_product_stock_with_reason(): void
    {
        $admin = User::where('role', UserRole::STORE_ADMIN)->firstOrFail();

        // Add 20 pcs
        $response = $this->actingAs($admin)->post(route('admin.products.adjust_stock', $this->product), [
            'adjustment_type' => 'add',
            'quantity' => 20,
            'reason' => 'Restock Pengrajin',
        ]);
        $response->assertRedirect();
        $this->assertEquals(44, $this->product->fresh()->stock); // 24 + 20 = 44

        // Subtract 4 pcs
        $this->actingAs($admin)->post(route('admin.products.adjust_stock', $this->product), [
            'adjustment_type' => 'subtract',
            'quantity' => 4,
            'reason' => 'Barang Cacat',
        ]);
        $this->assertEquals(40, $this->product->fresh()->stock);

        // Set to 0 pcs -> should set badge to Sold out
        $this->actingAs($admin)->post(route('admin.products.adjust_stock', $this->product), [
            'adjustment_type' => 'set',
            'quantity' => 0,
            'reason' => 'Habis',
        ]);
        $this->assertEquals(0, $this->product->fresh()->stock);
        $this->assertEquals('Sold out', $this->product->fresh()->badge);
    }

    public function test_admin_can_delete_unused_product(): void
    {
        $admin = User::where('role', UserRole::STORE_ADMIN)->firstOrFail();
        $category = Category::firstOrFail();

        $tempProduct = Product::create([
            'category_id' => $category->id,
            'sku' => 'SKU-TEMP-01',
            'name' => 'Temp Charm',
            'slug' => 'temp-charm',
            'price' => 15.00,
            'stock' => 5,
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.products.destroy', $tempProduct));
        $response->assertRedirect();
        $this->assertNull(Product::where('sku', 'SKU-TEMP-01')->first());
    }

    public function test_admin_can_access_customers_directory_menu(): void
    {
        $admin = User::where('role', UserRole::STORE_ADMIN)->firstOrFail();

        $response = $this->actingAs($admin)->get(route('admin.customers.index'));
        $response->assertStatus(200);
        $response->assertSee('Direktori Pelanggan &amp; Member', false);
        $response->assertSee('Clara Aurelia');
    }

    public function test_admin_can_access_categories_management_menu(): void
    {
        $admin = User::where('role', UserRole::STORE_ADMIN)->firstOrFail();

        $response = $this->actingAs($admin)->get(route('admin.categories.index'));
        $response->assertStatus(200);
        $response->assertSee('Klasifikasi &amp; Manajemen Kategori Perhiasan', false);
        $response->assertSee('Earrings');
    }

    public function test_admin_can_create_new_category(): void
    {
        $admin = User::where('role', UserRole::STORE_ADMIN)->firstOrFail();

        $response = $this->actingAs($admin)->post(route('admin.categories.store'), [
            'name' => 'Choker Collars',
            'slug' => 'choker-collars',
            'description' => 'Fine luxury velvet and diamond chokers.',
        ]);

        $response->assertRedirect();
        $category = Category::where('slug', 'choker-collars')->first();
        $this->assertNotNull($category);
        $this->assertEquals('Choker Collars', $category->name);
    }

    public function test_admin_can_update_category(): void
    {
        $admin = User::where('role', UserRole::STORE_ADMIN)->firstOrFail();
        $category = Category::create([
            'name' => 'Old Rings',
            'slug' => 'old-rings',
            'description' => 'Old description',
        ]);

        $response = $this->actingAs($admin)->put(route('admin.categories.update', $category), [
            'name' => 'Luxury Rings',
            'slug' => 'luxury-rings',
            'description' => 'Updated fine gold rings.',
        ]);

        $response->assertRedirect();
        $this->assertEquals('Luxury Rings', $category->fresh()->name);
        $this->assertEquals('luxury-rings', $category->fresh()->slug);
    }

    public function test_admin_cannot_delete_category_with_products(): void
    {
        $admin = User::where('role', UserRole::STORE_ADMIN)->firstOrFail();
        $categoryWithProduct = $this->product->category;

        $response = $this->actingAs($admin)->delete(route('admin.categories.destroy', $categoryWithProduct));
        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertNotNull(Category::find($categoryWithProduct->id));
    }

    public function test_admin_can_delete_empty_category(): void
    {
        $admin = User::where('role', UserRole::STORE_ADMIN)->firstOrFail();
        $emptyCategory = Category::create([
            'name' => 'Empty Collection',
            'slug' => 'empty-collection',
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.categories.destroy', $emptyCategory));
        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertNull(Category::find($emptyCategory->id));
    }

    public function test_admin_can_manage_blog_posts(): void
    {
        $admin = User::where('role', UserRole::STORE_ADMIN)->firstOrFail();

        // 1. Access index
        $indexResponse = $this->actingAs($admin)->get(route('admin.blogs.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Manajemen Jurnal &amp; Artikel Editorial', false);

        // 2. Store new blog post
        $storeResponse = $this->actingAs($admin)->post(route('admin.blogs.store'), [
            'title' => 'Tips Merawat Perhiasan Emas 18K',
            'category' => 'Jewelry Care',
            'author' => 'Anya Geraldine',
            'excerpt' => 'Panduan lengkap merawat perhiasan emas agar tetap berkilau.',
            'content' => 'Isi artikel lengkap mengenai cara membersihkan emas 18 karat.',
        ]);
        $storeResponse->assertRedirect();
        $post = BlogPost::where('slug', 'tips-merawat-perhiasan-emas-18k')->first();
        $this->assertNotNull($post);

        // 3. Update blog post
        $updateResponse = $this->actingAs($admin)->put(route('admin.blogs.update', $post), [
            'title' => 'Tips Merawat Perhiasan Emas & Berlian',
            'category' => 'Luxury Guide',
            'author' => 'Editorial Team',
            'excerpt' => 'Ringkasan terbaru.',
            'content' => 'Konten yang telah diperbarui.',
        ]);
        $updateResponse->assertRedirect();
        $this->assertEquals('Tips Merawat Perhiasan Emas & Berlian', $post->fresh()->title);
        $this->assertEquals('Luxury Guide', $post->fresh()->category);

        // 4. Delete blog post
        $deleteResponse = $this->actingAs($admin)->delete(route('admin.blogs.destroy', $post));
        $deleteResponse->assertRedirect();
        $this->assertNull(BlogPost::find($post->id));
    }

    public function test_visitor_can_send_livechat_message_via_ajax(): void
    {
        $response = $this->postJson(route('livechat.send'), [
            'name' => 'Raisa Andriana',
            'email' => 'raisa@example.com',
            'message' => 'Halo, apakah cincin ukuran 6 tersedia?',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $inquiry = Inquiry::where('email', 'raisa@example.com')->first();
        $this->assertNotNull($inquiry);
        $this->assertEquals('livechat', $inquiry->channel);
        $this->assertEquals('ai', $inquiry->handling_mode);
        $this->assertCount(2, $inquiry->messages); // 1 customer + 1 AI reply
    }

    public function test_ai_livechat_checks_product_price_and_stock_from_database(): void
    {
        $response = $this->postJson(route('livechat.send'), [
            'name' => 'Siti Nurhaliza',
            'email' => 'siti@example.com',
            'message' => 'Halo, tolong cek harga dan stok Lira Earrings',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.handled_by', 'ai');
        $response->assertJsonPath('data.handling_mode', 'ai');

        $aiReply = (string) $response->json('data.ai_reply');
        $this->assertStringContainsString('Lira Earrings', $aiReply);
        $this->assertStringContainsString('$ 20.00', $aiReply);
        $this->assertStringContainsString('24 pcs', $aiReply);
    }

    public function test_ai_livechat_enforces_boundary_guardrails_and_escalates_to_human_admin(): void
    {
        AiKnowledgeEntry::create([
            'title' => 'Batasan Komplain & Pengembalian Dana (Refund)',
            'type' => 'boundary',
            'keywords' => 'komplain, rusak, refund',
            'content' => 'AI tidak berwenang memproses klaim pengembalian dana secara sepihak. Tiket dialihkan ke Admin.',
            'requires_human_handoff' => true,
            'is_active' => true,
        ]);

        // 1. Customer triggers boundary keyword ("refund")
        $res1 = $this->postJson(route('livechat.send'), [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'message' => 'Pesanan saya rusak, saya minta refund uang kembali!',
        ]);

        $res1->assertStatus(200);
        $res1->assertJsonPath('data.handled_by', 'handoff');
        $res1->assertJsonPath('data.handling_mode', 'human');
        $this->assertStringContainsString('Batasan Layanan AI', (string) $res1->json('data.ai_reply'));

        $ticketNumber = (string) $res1->json('data.ticket_number');
        $inquiry = Inquiry::where('ticket_number', $ticketNumber)->firstOrFail();
        $this->assertEquals('human', $inquiry->handling_mode);

        // 2. Subsequent message on the same ticket is NOT handled by AI because it is in human mode
        $res2 = $this->postJson(route('livechat.send'), [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'ticket_number' => $ticketNumber,
            'message' => 'Ini foto bukti barang rusaknya ya min.',
        ]);

        $res2->assertStatus(200);
        $res2->assertJsonPath('data.handled_by', 'human');
        $res2->assertJsonPath('data.handling_mode', 'human');
        $this->assertNull($res2->json('data.ai_reply'));
    }

    public function test_admin_can_manage_ai_knowledge_base_toggle_mode_and_generate_ai_draft(): void
    {
        $admin = User::where('role', UserRole::STORE_ADMIN)->firstOrFail();

        // 1. Admin creates a new AI Knowledge Base entry
        $storeRes = $this->actingAs($admin)->post(route('admin.ai_knowledge.store'), [
            'title' => 'Info Perawatan Mutiara Air Tawar',
            'type' => 'knowledge',
            'keywords' => 'perawatan mutiara, bersihkan mutiara',
            'content' => 'Lap mutiara menggunakan kain microfiber lembap setelah dipakai.',
            'requires_human_handoff' => false,
            'is_active' => true,
        ]);
        $storeRes->assertRedirect();

        $entry = AiKnowledgeEntry::where('title', 'Info Perawatan Mutiara Air Tawar')->first();
        $this->assertNotNull($entry);

        // 2. Admin updates the entry
        $updateRes = $this->actingAs($admin)->put(route('admin.ai_knowledge.update', $entry), [
            'title' => 'Panduan Perawatan Mutiara Baroque',
            'type' => 'response_rule',
            'keywords' => 'mutiara baroque, cara rawat mutiara',
            'content' => 'Hindari semprotan parfum langsung pada permukaan mutiara.',
            'requires_human_handoff' => true,
            'is_active' => true,
        ]);
        $updateRes->assertRedirect();
        $this->assertEquals('Panduan Perawatan Mutiara Baroque', $entry->fresh()->title);
        $this->assertTrue($entry->fresh()->requires_human_handoff);

        // 3. Admin toggles inquiry mode & requests AI Draft
        $inquiry = Inquiry::create([
            'ticket_number' => 'TCK-DRAFT-01',
            'name' => 'Putri Marino',
            'email' => 'putri@example.com',
            'subject' => 'Tanya Lira Earrings',
            'channel' => 'livechat',
            'handling_mode' => 'ai',
            'status' => InquiryStatus::UNREAD,
        ]);
        $inquiry->messages()->create([
            'sender_type' => 'customer',
            'message' => 'Berapa harga Lira Earrings dan apakah stoknya masih ada?',
            'created_at' => now(),
        ]);

        $modeRes = $this->actingAs($admin)->postJson(route('admin.inquiries.mode', $inquiry), [
            'handling_mode' => 'human',
        ]);
        $modeRes->assertStatus(200);
        $this->assertEquals('human', $inquiry->fresh()->handling_mode);

        $draftRes = $this->actingAs($admin)->getJson(route('admin.inquiries.ai_draft', $inquiry));
        $draftRes->assertStatus(200);
        $this->assertStringContainsString('Lira Earrings', (string) $draftRes->json('data.draft'));

        // 4. Admin deletes the knowledge entry
        $delRes = $this->actingAs($admin)->delete(route('admin.ai_knowledge.destroy', $entry));
        $delRes->assertRedirect();
        $this->assertNull(AiKnowledgeEntry::find($entry->id));
    }

    public function test_customer_can_register_with_complete_shipping_data_and_auto_fill_checkout(): void
    {
        $response = $this->post(route('account.register'), [
            'name' => 'Nadia Putri',
            'email' => 'nadia.putri@example.com',
            'phone' => '081299887766',
            'province' => 'Jawa Barat',
            'city' => 'Kota Bandung',
            'district' => 'Coblong',
            'postcode' => '40135',
            'address_label' => 'Rumah',
            'address' => 'Jl. Ir. H. Juanda No. 108',
            'address_notes' => 'Sebelah Apotek Dago',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertRedirect(route('account.index'));
        $this->assertAuthenticated();

        $user = User::where('email', 'nadia.putri@example.com')->firstOrFail();
        $this->assertEquals('081299887766', $user->phone);
        $this->assertEquals('Kota Bandung', $user->city);
        $this->assertEquals('Jl. Ir. H. Juanda No. 108', $user->address);
        $this->assertCount(1, $user->address_book);

        // Verify Checkout page pre-fills registered user data
        $checkoutView = $this->actingAs($user)->get(route('checkout.index'));
        $checkoutView->assertStatus(200);
        $checkoutView->assertSee('Nadia');
        $checkoutView->assertSee('Putri');
        $checkoutView->assertSee('081299887766');
        $checkoutView->assertSee('Kota Bandung');
        $checkoutView->assertSee('Jl. Ir. H. Juanda No. 108');
    }

    public function test_customer_can_update_profile_shipping_addresses_bank_and_password(): void
    {
        $buyer = User::where('role', UserRole::CUSTOMER)->firstOrFail();

        // 1. Update Profile & Contact
        $profileResponse = $this->actingAs($buyer)->put(route('account.profile.update'), [
            'name' => 'Clara Aurelia Wijaya',
            'username' => 'clara.wijaya',
            'email' => $buyer->email,
            'phone' => '081122334455',
            'gender' => 'Perempuan',
            'birth_date' => '1997-08-24',
            'email_order' => '1',
            'whatsapp_shipping' => '1',
        ]);
        $profileResponse->assertRedirect();
        $this->assertEquals('Clara Aurelia Wijaya', $buyer->fresh()->name);
        $this->assertEquals('081122334455', $buyer->fresh()->phone);

        // 2. Add New Shipping Address & Set as Primary
        $addrResponse = $this->actingAs($buyer)->put(route('account.address.update'), [
            'action_type' => 'add_address',
            'recipient_name' => 'Clara Aurelia (Studio)',
            'phone' => '081122334455',
            'address_label' => 'Kantor',
            'country' => 'Indonesia',
            'province' => 'DKI Jakarta',
            'city' => 'Jakarta Pusat',
            'district' => 'Menteng',
            'postcode' => '10310',
            'address' => 'Jl. Thamrin No. 99',
            'address_notes' => 'Gedung Plaza Lt. 5',
            'set_as_primary' => '1',
        ]);
        $addrResponse->assertRedirect();
        $this->assertEquals('Jakarta Pusat', $buyer->fresh()->city);
        $this->assertEquals('Jl. Thamrin No. 99', $buyer->fresh()->address);
        $this->assertEquals('Kantor', $buyer->fresh()->address_label);

        // 3. Update Bank Account
        $bankResponse = $this->actingAs($buyer)->put(route('account.bank.update'), [
            'bank_name' => 'BCA',
            'account_number' => '8899001122',
            'account_holder' => 'Clara Aurelia Wijaya',
        ]);
        $bankResponse->assertRedirect();
        $this->assertEquals('8899001122', $buyer->fresh()->bank_account['account_number']);

        // 4. Update Password
        $pwdResponse = $this->actingAs($buyer)->put(route('account.password.update'), [
            'current_password' => 'buyer123',
            'password' => 'newpass123',
            'password_confirmation' => 'newpass123',
        ]);
        $pwdResponse->assertRedirect();
        $this->assertTrue(Hash::check('newpass123', $buyer->fresh()->password));
    }

    public function test_faq_page_is_accessible_and_renders_questions(): void
    {
        $response = $this->get(route('faq'));
        $response->assertStatus(200);
        $response->assertSee('Frequently Asked Questions (FAQ)');
        $response->assertSee('Bagaimana cara memesan perhiasan di PUTRI.J?');
        $response->assertSee('application/ld+json', false);
    }
}
