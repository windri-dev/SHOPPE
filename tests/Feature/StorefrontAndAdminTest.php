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
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;
use App\Services\AiConciergeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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
            'name' => 'Dresses',
            'slug' => 'dresses',
            'description' => 'Midi and maxi dresses.',
        ]);

        $this->product = Product::create([
            'category_id' => $category->id,
            'sku' => 'SKU-0012',
            'name' => 'Lira Silk Dress',
            'slug' => 'lira-silk-dress',
            'price' => 20.00,
            'stock' => 24,
            'is_featured' => true,
            'is_active' => true,
            'sales_count' => 48,
            'description' => 'Cast in silk satin blend.',
            'images' => ['images/product-1.png'],
            'video_url' => 'https://example.com/dress.mp4',
        ]);

        $this->blogPost = BlogPost::create([
            'title' => 'Tren Musim Semi Terkini',
            'title_en' => 'Top Trends From Spring',
            'slug' => 'top-trends-from-spring',
            'category' => 'Fashion',
            'author' => 'ANNY JOHNSON',
            'image' => 'images/blog-1.png',
            'excerpt' => 'Siluet emas minimalis dan bentuk skulptural.',
            'excerpt_en' => 'Minimalist gold silhouettes and sculptural forms.',
            'content' => 'Musim semi membawa apresiasi baru terhadap layering elegan.',
            'content_en' => 'Spring brings a renewed appreciation for effortless layering.',
            'published_at' => now(),
        ]);

        User::create([
            'name' => 'PUTRIJALVA Store Manager',
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
        $response->assertSee('PUTRIJALVA');
        $response->assertSee('Koleksi Terbaru');

        // English version sees "Shop The Latest"
        $enResponse = $this->withSession(['locale' => 'en'])->get('/');
        $enResponse->assertStatus(200);
        $enResponse->assertSee('Shop The Latest');
    }

    public function test_shop_catalog_and_product_detail_pages_are_accessible(): void
    {
        $catalogResponse = $this->get('/shop');
        $catalogResponse->assertStatus(200);
        $catalogResponse->assertSee('Lira Silk Dress');

        $detailResponse = $this->get('/shop/'.$this->product->slug);
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('Lira Silk Dress');
        $detailResponse->assertSee('SKU-0012');
    }

    public function test_blog_index_and_single_article_pages_are_accessible(): void
    {
        $indexResponse = $this->withSession(['locale' => 'id'])->get('/blog');
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Tren Musim Semi Terkini');
        $indexResponse->assertSee('Siluet emas minimalis dan bentuk skulptural.');

        $enIndexResponse = $this->withSession(['locale' => 'en'])->get('/blog');
        $enIndexResponse->assertStatus(200);
        $enIndexResponse->assertSee('Top Trends From Spring');
        $enIndexResponse->assertSee('Minimalist gold silhouettes and sculptural forms.');

        $showResponse = $this->withSession(['locale' => 'id'])->get('/blog/'.$this->blogPost->slug);
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Tren Musim Semi Terkini');
        $showResponse->assertSee('ANNY JOHNSON');

        $enShowResponse = $this->withSession(['locale' => 'en'])->get('/blog/'.$this->blogPost->slug);
        $enShowResponse->assertStatus(200);
        $enShowResponse->assertSee('Top Trends From Spring');
        $enShowResponse->assertSee('Spring brings a renewed appreciation for effortless layering.');
    }

    public function test_guest_can_access_checkout_and_process_order_without_login_with_complete_info_and_phone(): void
    {
        // 1. Guest can access /checkout directly without being redirected to login
        $response = $this->get('/checkout');
        $response->assertStatus(200);
        $response->assertSee('Detail Penerima &amp; Alamat Pengiriman', false);
        $response->assertSee('+62 812-3456-7890');

        // 2. Checkout validation requires phone number for communication
        $invalidResponse = $this->postJson('/checkout', [
            'first_name' => 'Tamu',
            'last_name' => 'Anonim',
            'email' => 'guest@example.com',
            'phone' => '', // missing phone
            'country' => 'Indonesia',
            'street_address' => 'Jl. Senopati No. 12',
            'city' => 'Jakarta Selatan',
            'postcode' => '12190',
            'payment_method' => 'bank_transfer',
            'items' => [
                ['id' => $this->product->id, 'quantity' => 1],
            ],
        ]);
        $invalidResponse->assertStatus(422);
        $invalidResponse->assertJsonValidationErrors(['phone']);

        // 3. Guest can submit checkout with complete info and reachable phone number
        $jsonResponse = $this->postJson('/checkout', [
            'first_name' => 'Tamu',
            'last_name' => 'Anonim',
            'email' => 'guest@example.com',
            'phone' => '081298765432',
            'country' => 'Indonesia',
            'province' => 'DKI Jakarta',
            'district' => 'Kebayoran Baru',
            'city' => 'Jakarta Selatan',
            'street_address' => 'Jl. Senopati No. 12, RT 01 / RW 02',
            'postcode' => '12190',
            'payment_method' => 'bank_transfer',
            'items' => [
                ['id' => $this->product->id, 'quantity' => 1],
            ],
        ]);
        $jsonResponse->assertStatus(200);
        $jsonResponse->assertJson(['success' => true]);

        $orderNumber = $jsonResponse->json('order_number');
        $this->assertNotNull($orderNumber);

        // Verify order created in database with null user_id and valid phone
        $order = Order::where('order_number', $orderNumber)->first();
        $this->assertNotNull($order);
        $this->assertNull($order->user_id);
        $this->assertEquals('081298765432', $order->customer_phone);
        $this->assertEquals('guest@example.com', $order->customer_email);
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
        $orderPage->assertSee('Lira Silk Dress');
    }

    public function test_contact_inquiry_submission_stores_inquiry_and_initial_message(): void
    {
        $this->get('/contact')->assertStatus(200);

        $response = $this->post('/contact', [
            'name' => 'Clara Aurelia',
            'email' => 'buyer@shoppe.com',
            'phone' => '+628123456789',
            'subject' => 'Custom Request',
            'message' => 'Can I request custom size on the Lira Silk Dress?',
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
        $response->assertSee('Lira Silk Dress');
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
        $response->assertSee('Klasifikasi &amp; Manajemen Kategori Produk', false);
        $response->assertSee('Dresses');
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
            'message' => 'Halo, tolong cek harga dan stok Lira Silk Dress',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.handled_by', 'ai');
        $response->assertJsonPath('data.handling_mode', 'ai');

        $aiReply = (string) $response->json('data.ai_reply');
        $this->assertStringContainsString('Lira Silk Dress', $aiReply);
        $this->assertStringContainsString($this->product->formatted_price, $aiReply);
        $this->assertStringContainsString('24 pcs', $aiReply);
    }

    public function test_ai_livechat_provides_styling_recommendation_with_ready_stock_products(): void
    {
        $response = $this->postJson(route('livechat.send'), [
            'name' => 'Maya Aesthetic',
            'email' => 'maya@example.com',
            'message' => 'carikkan aku busana yang cocok dan rekomendasi yang tepat',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.handled_by', 'ai');
        $reply = (string) $response->json('data.ai_reply');

        $this->assertStringContainsString('rekomendasi busana pilihan terfavorit', $reply);
        $this->assertStringNotContainsString('Habis', $reply);
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
            'subject' => 'Tanya Lira Silk Dress',
            'channel' => 'livechat',
            'handling_mode' => 'ai',
            'status' => InquiryStatus::UNREAD,
        ]);
        $inquiry->messages()->create([
            'sender_type' => 'customer',
            'message' => 'Berapa harga Lira Silk Dress dan apakah stoknya masih ada?',
            'created_at' => now(),
        ]);

        $modeRes = $this->actingAs($admin)->postJson(route('admin.inquiries.mode', $inquiry), [
            'handling_mode' => 'human',
        ]);
        $modeRes->assertStatus(200);
        $this->assertEquals('human', $inquiry->fresh()->handling_mode);

        $draftRes = $this->actingAs($admin)->getJson(route('admin.inquiries.ai_draft', $inquiry));
        $draftRes->assertStatus(200);
        $this->assertStringContainsString('Lira Silk Dress', (string) $draftRes->json('data.draft'));

        // 4. Admin deletes the knowledge entry
        $delRes = $this->actingAs($admin)->delete(route('admin.ai_knowledge.destroy', $entry));
        $delRes->assertRedirect();
        $this->assertNull(AiKnowledgeEntry::find($entry->id));
    }

    public function test_ai_knowledge_can_be_saved_from_chat_and_retrieved_for_future_questions(): void
    {
        $admin = User::where('role', UserRole::STORE_ADMIN)->firstOrFail();
        $concierge = app(AiConciergeService::class);

        // 1. Test keyword extraction
        $keywords = $concierge->extractKeywordsFromText('Bagaimana cara merawat kain sutra silk agar awet?');
        $this->assertStringContainsString('merawat', $keywords);
        $this->assertStringContainsString('sutra', $keywords);

        // 2. Test autoLearnKnowledge
        $created = $concierge->autoLearnKnowledge(
            'Bagaimana cara merawat kain sutra silk agar awet?',
            'Cuci kain sutra PUTRIJALVA dengan air dingin, deterjen lembut khusus sutra, dan jangan diperas kencang.'
        );
        $this->assertNotNull($created);
        $this->assertEquals('knowledge', $created->type);

        // 3. Test that subsequent similar question is directly answered from the saved knowledge entry
        $result = $concierge->handleCustomerMessage('cara merawat kain sutra silk');
        $this->assertEquals($created->title, $result['rule_applied']);
        $this->assertStringContainsString('Cuci kain sutra PUTRIJALVA', $result['reply']);

        // 4. Test admin reply with save_as_knowledge flag
        $inquiry = Inquiry::create([
            'ticket_number' => 'TCK-LEARN-01',
            'name' => 'Siti Rahma',
            'email' => 'siti@example.com',
            'subject' => 'Tanya Pengiriman Sameday',
            'channel' => 'livechat',
            'handling_mode' => 'human',
            'status' => InquiryStatus::UNREAD,
        ]);
        $inquiry->messages()->create([
            'sender_type' => 'customer',
            'message' => 'Apakah butik melayani pengiriman sameday di area Jakarta?',
            'created_at' => now(),
        ]);

        $replyRes = $this->actingAs($admin)->postJson(route('admin.inquiries.reply', $inquiry), [
            'message' => 'Ya, kami melayani pengiriman Sameday & Instant untuk seluruh wilayah Jakarta.',
            'save_as_knowledge' => true,
        ]);
        $replyRes->assertStatus(200);
        $this->assertTrue($replyRes->json('data.knowledge_saved'));

        $savedFromReply = AiKnowledgeEntry::where('content', 'Ya, kami melayani pengiriman Sameday & Instant untuk seluruh wilayah Jakarta.')->first();
        $this->assertNotNull($savedFromReply);

        // 5. Test that a customer asking about sameday in Jakarta now receives this saved knowledge
        $subsequentResult = $concierge->handleCustomerMessage('apakah bisa pengiriman sameday jakarta?');
        $this->assertEquals($savedFromReply->title, $subsequentResult['rule_applied']);
        $this->assertStringContainsString('Ya, kami melayani pengiriman Sameday & Instant', $subsequentResult['reply']);
    }

    public function test_user_can_switch_language_between_indonesian_and_english(): void
    {
        // 1. Initial request in default locale (id)
        $homeId = $this->get(route('home'));
        $homeId->assertStatus(200);
        $homeId->assertSee('Koleksi');
        $homeId->assertSee(route('locale.switch', 'en'));

        // 2. Switch to English
        $switchEn = $this->get(route('locale.switch', 'en'));
        $switchEn->assertRedirect();
        $switchEn->assertSessionHas('locale', 'en');

        // 3. Request home with English session
        $homeEn = $this->withSession(['locale' => 'en'])->get(route('home'));
        $homeEn->assertStatus(200);
        $homeEn->assertSee('Shop');
        $homeEn->assertSee(route('locale.switch', 'id'));

        // 4. Test invalid locale safely rejected
        $switchInvalid = $this->get(route('locale.switch', 'fr'));
        $switchInvalid->assertRedirect();
        $this->assertNotEquals('fr', session('locale'));
    }

    public function test_product_auto_translate_and_bilingual_display(): void
    {
        $admin = User::where('role', UserRole::STORE_ADMIN)->firstOrFail();
        $category = Category::firstOrFail();

        // 1. Test AJAX auto-translate endpoint for admin preview
        $ajaxResponse = $this->actingAs($admin)->postJson(route('admin.products.auto_translate'), [
            'name' => 'Gaun Pesta Sutra Modern',
            'description' => 'Gaun sutra premium dengan potongan elegan untuk pesta malam.',
        ]);
        $ajaxResponse->assertStatus(200);
        $ajaxResponse->assertJsonStructure([
            'status',
            'name_en',
            'description_en',
        ]);
        $this->assertEquals('success', $ajaxResponse->json('status'));
        $this->assertNotEmpty($ajaxResponse->json('name_en'));

        // 2. Test admin store product without English fields auto-populates them
        $storeResponse = $this->actingAs($admin)->post(route('admin.products.store'), [
            'name' => 'Gaun Sutra Elegan Batik',
            'name_en' => '', // leave empty to test auto AI translation
            'category_id' => $category->id,
            'sku' => 'SKU-AI-TRANS-1',
            'price' => 150.00,
            'stock' => 12,
            'description' => 'Kain sutra asli berpadu corak batik kontemporer.',
            'description_en' => '', // leave empty to test auto AI translation
        ]);
        $storeResponse->assertRedirect();

        $savedProduct = Product::where('sku', 'SKU-AI-TRANS-1')->firstOrFail();
        $this->assertEquals('Gaun Sutra Elegan Batik', $savedProduct->getRawName());
        $this->assertNotEmpty($savedProduct->name_en);
        $this->assertNotEmpty($savedProduct->description_en);

        // 3. Test dynamic accessor behavior based on locale
        app()->setLocale('id');
        $this->assertEquals('Gaun Sutra Elegan Batik', $savedProduct->name);

        app()->setLocale('en');
        $this->assertEquals($savedProduct->name_en, $savedProduct->name);
        $this->assertEquals($savedProduct->description_en, $savedProduct->description);

        // 4. Test storefront response in English session
        $storefrontEn = $this->withSession(['locale' => 'en'])->get(route('shop.show', $savedProduct->slug));
        $storefrontEn->assertStatus(200);
        $storefrontEn->assertSee($savedProduct->name_en);

        // Reset locale back to ID
        app()->setLocale('id');
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
        $response->assertSee('Bagaimana cara memesan busana di PUTRIJALVA?');
        $response->assertSee('application/ld+json', false);
    }

    public function test_product_with_video_renders_video_elements_on_detail_page(): void
    {
        $productWithVideo = Product::whereNotNull('video_url')->firstOrFail();
        $this->assertTrue($productWithVideo->hasVideo());

        $response = $this->get(route('shop.show', $productWithVideo->slug));
        $response->assertStatus(200);
        $response->assertSee('pdpThumbVideoBtn');
        $response->assertSee('pdpMainVideoBox');
        $response->assertSee('Lihat Video Busana');
    }

    public function test_product_without_video_does_not_render_video_elements(): void
    {
        $this->product->update(['video_url' => null]);
        $response = $this->get(route('shop.show', $this->product->slug));
        $response->assertStatus(200);
        $response->assertDontSee('id="pdpThumbVideoBtn"', false);
        $response->assertDontSee('id="pdpFloatingPlayBtn"', false);
        $response->assertDontSee('id="pdpMainVideoBox"', false);
        $response->assertDontSee('Video Showcase');
    }

    public function test_product_card_does_not_render_video_badge(): void
    {
        $response = $this->get(route('shop.index'));
        $response->assertStatus(200);
        $response->assertDontSee('product-video-badge');
    }

    public function test_product_model_video_type_helpers(): void
    {
        $p1 = new Product(['video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ']);
        $this->assertEquals('youtube', $p1->video_type);
        $this->assertStringContainsString('youtube.com/embed/dQw4w9WgXcQ', (string) $p1->video_embed_url);

        $p2 = new Product(['video_url' => 'https://example.com/fashion-lookbook.mp4']);
        $this->assertEquals('direct', $p2->video_type);
        $this->assertEquals('https://example.com/fashion-lookbook.mp4', $p2->video_embed_url);
    }

    public function test_product_detail_page_renders_reviews_and_rating_summary(): void
    {
        ProductReview::create([
            'product_id' => $this->product->id,
            'customer_name' => 'zainulll12',
            'rating' => 5,
            'size_purchased' => 'Size M, Champagne Silk',
            'quality_tag' => 'Sangat bagus & adem',
            'fit_tag' => 'Pas di badan',
            'comment' => 'barang mendarat dengan aman, seller responsif.',
            'seller_reply' => 'Hai Kak, Terimakasih sudah berbelanja di toko Kami.',
            'seller_replied_at' => now(),
            'is_verified_purchase' => true,
            'is_approved' => true,
        ]);

        $response = $this->get(route('shop.show', $this->product->slug));
        $response->assertStatus(200);
        $response->assertSee('Penilaian Produk');
        $response->assertSee('dari 5');
        $response->assertSee('Semua');
        $response->assertSee('5 Bintang');
        $response->assertSee('zainulll12');
        $response->assertSee('barang mendarat dengan aman, seller responsif.');
        $response->assertSee('Variasi: Size M, Champagne Silk');
        $response->assertSee('Kualitas Bahan:');
        $response->assertSee('Sangat bagus &amp; adem', false);
        $response->assertSee('Respon Penjual:');
        $response->assertSee('Hai Kak, Terimakasih sudah berbelanja di toko Kami.');
    }

    public function test_guest_can_submit_product_review(): void
    {
        $response = $this->post(route('shop.reviews.store', $this->product->slug), [
            'rating' => 5,
            'customer_name' => 'Maya Bella',
            'customer_email' => 'maya@example.com',
            'size_purchased' => 'S',
            'headline' => 'Sangat suka dengan warnanya',
            'comment' => 'Jahitannya rapi dan ukurannya pas di badan saya.',
        ]);

        $response->assertRedirect(route('shop.show', $this->product->slug).'#customer-reviews');
        $response->assertSessionHas('review_success');

        $this->assertDatabaseHas('product_reviews', [
            'product_id' => $this->product->id,
            'customer_name' => 'Maya Bella',
            'rating' => 5,
            'is_verified_purchase' => false,
        ]);
    }

    public function test_authenticated_buyer_with_past_order_gets_verified_purchase(): void
    {
        $buyer = User::factory()->create(['role' => UserRole::CUSTOMER]);
        $order = Order::create([
            'user_id' => $buyer->id,
            'order_number' => 'ORD-REV-001',
            'status' => OrderStatus::DELIVERED,
            'subtotal' => 20.00,
            'shipping_cost' => 0.00,
            'discount_amount' => 0.00,
            'grand_total' => 20.00,
            'customer_name' => $buyer->name,
            'customer_email' => $buyer->email,
            'customer_phone' => '08123456789',
            'shipping_address' => ['address' => 'Jl. Sudirman'],
            'payment_method' => 'manual_transfer',
            'payment_status' => 'paid',
        ]);
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'unit_price' => 20.00,
            'quantity' => 1,
            'line_total' => 20.00,
        ]);

        $response = $this->actingAs($buyer)->post(route('shop.reviews.store', $this->product->slug), [
            'rating' => 5,
            'customer_name' => $buyer->name,
            'size_purchased' => 'M',
            'headline' => 'Kualitas terbaik',
            'comment' => 'Bahan sangat adem dan potongan pas di badan.',
        ]);

        $response->assertRedirect(route('shop.show', $this->product->slug).'#customer-reviews');
        $this->assertDatabaseHas('product_reviews', [
            'product_id' => $this->product->id,
            'user_id' => $buyer->id,
            'is_verified_purchase' => true,
        ]);
    }

    public function test_review_helpful_counter_can_be_incremented(): void
    {
        $review = ProductReview::create([
            'product_id' => $this->product->id,
            'customer_name' => 'Anisa',
            'rating' => 5,
            'comment' => 'Ulasan yang sangat bermanfaat.',
            'helpful_count' => 3,
            'is_approved' => true,
        ]);

        $response = $this->postJson(route('reviews.helpful', $review->id));
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'helpful_count' => 4,
        ]);

        $this->assertEquals(4, $review->fresh()->helpful_count);
    }

    public function test_admin_can_upload_product_image_file_to_storage_server(): void
    {
        Storage::fake('public');

        $admin = User::where('role', UserRole::STORE_ADMIN)->firstOrFail();
        $category = Category::firstOrFail();
        $file = UploadedFile::fake()->image('produk-baru.jpg', 600, 600);

        $response = $this->from(route('admin.products.index'))
            ->actingAs($admin)
            ->post(route('admin.products.store'), [
                'name' => 'Gaun Pesta Mewah Satin',
                'category_id' => $category->id,
                'sku' => 'SKU-UPLOAD-01',
                'price' => 750000,
                'stock' => 15,
                'image' => $file,
                'description' => 'Gaun pesta mewah dari bahan satin berkualitas.',
            ]);

        $response->assertRedirect(route('admin.products.index'));

        $product = Product::where('sku', 'SKU-UPLOAD-01')->firstOrFail();
        $this->assertNotNull($product->images);
        $this->assertCount(1, $product->images);
        $this->assertStringStartsWith('products/', $product->images[0]);
        Storage::disk('public')->assertExists($product->images[0]);

        // Accessor should point to storage URL
        $this->assertStringContainsString('storage/products/', $product->first_image_url);

        // Update product with another image
        $newFile = UploadedFile::fake()->image('produk-update.png', 800, 800);
        $updateResponse = $this->from(route('admin.products.index'))
            ->actingAs($admin)
            ->put(route('admin.products.update', $product->id), [
                'name' => 'Gaun Pesta Mewah Satin Update',
                'category_id' => $category->id,
                'sku' => 'SKU-UPLOAD-01',
                'price' => 800000,
                'stock' => 10,
                'image' => $newFile,
                'description' => 'Deskripsi update.',
            ]);

        $updateResponse->assertRedirect(route('admin.products.index'));
        $product->refresh();
        $this->assertStringStartsWith('products/', $product->images[0]);
        Storage::disk('public')->assertExists($product->images[0]);
    }

    public function test_admin_can_upload_product_video_file_to_storage_server_and_replace_or_remove_it(): void
    {
        Storage::fake('public');

        $admin = User::where('role', UserRole::STORE_ADMIN)->firstOrFail();
        $category = Category::firstOrFail();
        $videoFile = UploadedFile::fake()->create('fashion-show.mp4', 2048, 'video/mp4');

        $response = $this->from(route('admin.products.index'))
            ->actingAs($admin)
            ->post(route('admin.products.store'), [
                'name' => 'Dress Video Test',
                'category_id' => $category->id,
                'sku' => 'SKU-VID-01',
                'price' => 500000,
                'stock' => 10,
                'video' => $videoFile,
                'description' => 'Produk dengan video catwalk.',
            ]);

        $response->assertRedirect(route('admin.products.index'));

        $product = Product::where('sku', 'SKU-VID-01')->firstOrFail();
        $this->assertNotNull($product->video_url);
        $this->assertStringStartsWith('products/videos/', $product->video_url);
        Storage::disk('public')->assertExists($product->video_url);

        $this->assertTrue($product->hasVideo());
        $this->assertEquals('direct', $product->video_type);
        $this->assertStringContainsString('storage/products/videos/', (string) $product->video_url_asset);

        // Update product with replacement video
        $newVideo = UploadedFile::fake()->create('fashion-new.mp4', 3072, 'video/mp4');
        $updateResponse = $this->from(route('admin.products.index'))
            ->actingAs($admin)
            ->put(route('admin.products.update', $product->id), [
                'name' => 'Dress Video Test Updated',
                'category_id' => $category->id,
                'sku' => 'SKU-VID-01',
                'price' => 550000,
                'stock' => 8,
                'video' => $newVideo,
                'description' => 'Produk dengan video catwalk baru.',
            ]);

        $updateResponse->assertRedirect(route('admin.products.index'));
        $product->refresh();
        $this->assertStringStartsWith('products/videos/', $product->video_url);
        Storage::disk('public')->assertExists($product->video_url);

        // Remove video
        $removeResponse = $this->from(route('admin.products.index'))
            ->actingAs($admin)
            ->put(route('admin.products.update', $product->id), [
                'name' => 'Dress Video Test Updated',
                'category_id' => $category->id,
                'sku' => 'SKU-VID-01',
                'price' => 550000,
                'stock' => 8,
                'remove_video' => 1,
                'video_url' => '',
                'description' => 'Produk tanpa video.',
            ]);

        $removeResponse->assertRedirect(route('admin.products.index'));
        $product->refresh();
        $this->assertNull($product->video_url);
        $this->assertFalse($product->hasVideo());
    }
}
