<?php

declare(strict_types=1);

use App\Http\Controllers\Web\AccountController;
use App\Http\Controllers\Web\BlogController;
use App\Http\Controllers\Web\CartController;
use App\Http\Controllers\Web\CheckoutController;
use App\Http\Controllers\Web\ContactController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\OrderController;
use App\Http\Controllers\Web\ShopController;
use App\Http\Controllers\Web\StaticPageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes (Storefront & Pelanggan Publik)
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

// Katalog & Detail Produk
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/{slug}', [ShopController::class, 'show'])->name('shop.show');

// Keranjang Belanja & Checkout
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
Route::get('/orders/{orderNumber}', [OrderController::class, 'show'])->name('order.show');
Route::post('/orders/{orderNumber}/confirm-payment', [OrderController::class, 'confirmPayment'])->name('order.confirm_payment');

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
