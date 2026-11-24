<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\BannerAdminController;
use App\Http\Controllers\Admin\BlogAdminController;
use App\Http\Controllers\Admin\CategoryAdminController;
use App\Http\Controllers\Admin\CustomerAdminController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LivechatAdminController;
use App\Http\Controllers\Admin\OrderAdminController;
use App\Http\Controllers\Admin\ProductAdminController;
use App\Http\Controllers\Admin\SettingAdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes (Khusus Pengelola Toko / Internal Management Only)
|--------------------------------------------------------------------------
*/

// Auth Gate Khusus Pengelola Toko
Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

// Dashboard & Manajemen Terproteksi (Role Middleware)
Route::middleware(['role:admin,store_admin,store_staff'])->group(function () {
    // 1. Overview & KPI
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // 2. Manajemen Pesanan & Verifikasi Pembayaran
    Route::get('/orders', [OrderAdminController::class, 'index'])->name('orders.index');
    Route::post('/orders/{order}/status', [OrderAdminController::class, 'updateStatus'])->name('orders.status');
    Route::post('/orders/{order}/payment', [OrderAdminController::class, 'updatePayment'])->name('orders.payment');

    // 3. Katalog & Manajemen Stok Produk (Tambah, Edit, Sesuaikan Stok, Hapus)
    Route::get('/products', [ProductAdminController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductAdminController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductAdminController::class, 'store'])->name('products.store');
    Route::post('/products/auto-translate', [ProductAdminController::class, 'autoTranslate'])->name('products.auto_translate');
    Route::put('/products/{product}', [ProductAdminController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductAdminController::class, 'destroy'])->name('products.destroy');
    Route::post('/products/{product}/adjust-stock', [ProductAdminController::class, 'adjustStock'])->name('products.adjust_stock');
    Route::match(['post', 'patch'], '/products/{product}/toggle-stock', [ProductAdminController::class, 'toggleStock'])->name('products.toggle_stock');

    // 4. Manajemen Kategori Produk (Tambah, Edit, Hapus Kategori)
    Route::get('/categories', [CategoryAdminController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryAdminController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [CategoryAdminController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryAdminController::class, 'destroy'])->name('categories.destroy');

    // 5. Manajemen Jurnal & Artikel Editorial
    Route::get('/blogs', [BlogAdminController::class, 'index'])->name('blogs.index');
    Route::post('/blogs', [BlogAdminController::class, 'store'])->name('blogs.store');
    Route::put('/blogs/{blog}', [BlogAdminController::class, 'update'])->name('blogs.update');
    Route::delete('/blogs/{blog}', [BlogAdminController::class, 'destroy'])->name('blogs.destroy');

    // 6. Direktori Pelanggan
    Route::get('/customers', [CustomerAdminController::class, 'index'])->name('customers.index');

    // 7. Live Chat & AI Concierge Hybrid (Mode AI / Admin Manusia & Knowledge Base)
    Route::get('/livechat', [LivechatAdminController::class, 'index'])->name('livechat');
    Route::get('/livechat/inquiries', [LivechatAdminController::class, 'inquiries'])->name('livechat.inquiries');
    Route::get('/inquiries/{inquiry}/messages', [LivechatAdminController::class, 'messages'])->name('inquiries.messages');
    Route::post('/inquiries/{inquiry}/reply', [LivechatAdminController::class, 'reply'])->name('inquiries.reply');
    Route::post('/inquiries/{inquiry}/status', [LivechatAdminController::class, 'updateStatus'])->name('inquiries.status');
    Route::post('/inquiries/{inquiry}/mode', [LivechatAdminController::class, 'toggleMode'])->name('inquiries.mode');
    Route::get('/inquiries/{inquiry}/ai-draft', [LivechatAdminController::class, 'aiDraft'])->name('inquiries.ai_draft');
    Route::post('/ai-knowledge', [LivechatAdminController::class, 'storeKnowledge'])->name('ai_knowledge.store');
    Route::put('/ai-knowledge/{entry}', [LivechatAdminController::class, 'updateKnowledge'])->name('ai_knowledge.update');
    Route::delete('/ai-knowledge/{entry}', [LivechatAdminController::class, 'destroyKnowledge'])->name('ai_knowledge.destroy');

    // 8. Manajemen Banner & Slider Carousel Beranda
    Route::get('/banners', [BannerAdminController::class, 'index'])->name('banners.index');
    Route::post('/banners', [BannerAdminController::class, 'store'])->name('banners.store');
    Route::post('/banners/slider-settings', [BannerAdminController::class, 'updateSliderSettings'])->name('banners.slider_settings');
    Route::put('/banners/{banner}', [BannerAdminController::class, 'update'])->name('banners.update');
    Route::delete('/banners/{banner}', [BannerAdminController::class, 'destroy'])->name('banners.destroy');
    Route::post('/banners/{banner}/toggle', [BannerAdminController::class, 'toggle'])->name('banners.toggle');

    // 9. Pengaturan Toko: Rekening Bank Pembayaran & Akun Sosial Media
    Route::get('/settings', [SettingAdminController::class, 'index'])->name('settings.index');
    Route::post('/settings/social', [SettingAdminController::class, 'updateSocial'])->name('settings.social');
    Route::post('/settings/banks', [SettingAdminController::class, 'storeBank'])->name('settings.banks.store');
    Route::put('/settings/banks/{bank}', [SettingAdminController::class, 'updateBank'])->name('settings.banks.update');
    Route::post('/settings/banks/{bank}/toggle', [SettingAdminController::class, 'toggleBank'])->name('settings.banks.toggle');
    Route::delete('/settings/banks/{bank}', [SettingAdminController::class, 'destroyBank'])->name('settings.banks.destroy');
});
