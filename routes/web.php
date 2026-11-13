<?php

declare(strict_types=1);

use App\Http\Controllers\Web\AccountController;
use App\Http\Controllers\Web\BlogController;
use App\Http\Controllers\Web\CartController;
use App\Http\Controllers\Web\CheckoutController;
use App\Http\Controllers\Web\ContactController;
use App\Http\Controllers\Web\GoogleAuthController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\LocaleController;
use App\Http\Controllers\Web\OrderController;
use App\Http\Controllers\Web\PaymentWebhookController;
use App\Http\Controllers\Web\ProductReviewController;
use App\Http\Controllers\Web\ShopController;
use App\Http\Controllers\Web\StaticPageController;
use App\Http\Controllers\Web\WishlistController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes (Storefront & Pelanggan Publik)
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

// Katalog & Detail Produk
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/{slug}', [ShopController::class, 'show'])->name('shop.show');
Route::post('/shop/{product:slug}/reviews', [ProductReviewController::class, 'store'])->name('shop.reviews.store');
Route::post('/reviews/{review}/helpful', [ProductReviewController::class, 'helpful'])->name('reviews.helpful');

// Keranjang Belanja
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');

// Wishlist / Daftar Keinginan
Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
Route::delete('/wishlist/{product}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');
Route::get('/wishlist/count', [WishlistController::class, 'count'])->name('wishlist.count');

// Checkout (Bisa Checkout Langsung / Guest Checkout Tanpa Login)
Route::post('/shipping/calculate', [CheckoutController::class, 'calculateShipping'])->name('shipping.calculate');
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');

// Orders & Payment Integration
Route::get('/orders/{orderNumber}', [OrderController::class, 'show'])->name('order.show');
Route::get('/orders/{orderNumber}/status', [OrderController::class, 'checkStatus'])->name('order.check_status');
Route::post('/orders/{orderNumber}/initiate-pga', [OrderController::class, 'initiatePgaPayment'])->name('order.initiate_pga');
Route::post('/orders/{orderNumber}/confirm-payment', [OrderController::class, 'confirmPayment'])->name('order.confirm_payment');

// Webhook Callback Payment Gateway (CSRF exempt)
Route::post('/payment/webhook/{provider?}', [PaymentWebhookController::class, 'handle'])->name('payment.webhook');

// Blog & Jurnal
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

// Halaman Statis & Kontak
Route::get('/about', [StaticPageController::class, 'about'])->name('about');
Route::get('/faq', [StaticPageController::class, 'faq'])->name('faq');
Route::get('/privacy-policy', [StaticPageController::class, 'privacy'])->name('privacy');
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');
Route::post('/livechat/message', [ContactController::class, 'livechatSubmit'])->name('livechat.send');
Route::get('/livechat/thread/{ticketNumber}', [ContactController::class, 'livechatThread'])->name('livechat.thread');

// Default Login route alias
Route::get('/login', [AccountController::class, 'index'])->name('login');
Route::post('/login', [AccountController::class, 'login']);

// Google OAuth Single Sign-On (SSO)
Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');

// Akun Pelanggan (Customer Only)
Route::prefix('account')->name('account.')->group(function () {
    Route::get('/', [AccountController::class, 'index'])->name('index');
    Route::post('/login', [AccountController::class, 'login'])->name('login');
    Route::post('/register', [AccountController::class, 'register'])->name('register');
    Route::post('/logout', [AccountController::class, 'logout'])->name('logout');

    Route::middleware('auth')->group(function () {
        Route::put('/profile', [AccountController::class, 'updateProfile'])->name('profile.update');
        Route::put('/shipping-address', [AccountController::class, 'updateShippingAddress'])->name('address.update');
        Route::put('/bank-account', [AccountController::class, 'updateBankAccount'])->name('bank.update');
        Route::put('/password', [AccountController::class, 'updatePassword'])->name('password.update');
    });
});
