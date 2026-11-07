<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;

/*
|--------------------------------------------------------------------------
| Admin Routes (Khusus Pengelola Toko / Internal Management Only)
|--------------------------------------------------------------------------
*/

// Auth Gate Khusus Pengelola Toko
Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

// Dashboard Terproteksi (Role Middleware memvalidasi auth dan hak akses pengelola toko)
Route::middleware(['role:admin,store_admin,store_staff'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/orders/{order}/status', [DashboardController::class, 'updateOrderStatus'])->name('orders.status');
    Route::post('/products/{product}/toggle-stock', [DashboardController::class, 'toggleProductStock'])->name('products.toggle_stock');
    Route::post('/inquiries/{inquiry}/reply', [DashboardController::class, 'replyInquiry'])->name('inquiries.reply');
});
