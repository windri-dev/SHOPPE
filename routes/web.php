<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\ShopController;
use App\Http\Controllers\Web\CartController;
use App\Http\Controllers\Web\CheckoutController;
use App\Http\Controllers\Web\OrderController;
use App\Http\Controllers\Web\AccountController;
use App\Http\Controllers\Web\ContactController;
use App\Http\Controllers\Web\BlogController;
use App\Http\Controllers\Web\StaticPageController;

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

// Blog & Jurnal
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

// Halaman Statis & Kontak
Route::get('/about', [StaticPageController::class, 'about'])->name('about');
Route::get('/privacy-policy', [StaticPageController::class, 'privacy'])->name('privacy');
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');

// Default Login route alias
Route::get('/login', [AccountController::class, 'index'])->name('login');
Route::post('/login', [AccountController::class, 'login']);

// Akun Pelanggan (Customer Only)
Route::prefix('account')->name('account.')->group(function () {
    Route::get('/', [AccountController::class, 'index'])->name('index');
    Route::post('/login', [AccountController::class, 'login'])->name('login');
    Route::post('/register', [AccountController::class, 'register'])->name('register');
    Route::post('/logout', [AccountController::class, 'logout'])->name('logout');
});
