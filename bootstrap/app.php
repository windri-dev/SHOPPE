<?php

use App\Http\Middleware\AdminIpWhitelistMiddleware;
use App\Http\Middleware\EnsureUserHasRole;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\TrackStorefrontTraffic;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Register Route Khusus Pengelola Toko dengan Proteksi IP Whitelist
            Route::middleware(['web', 'admin.ip'])
                ->prefix('admin')
                ->as('admin.')
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            SetLocale::class,
            TrackStorefrontTraffic::class,
        ]);

        $middleware->alias([
            'role' => EnsureUserHasRole::class,
            'admin.ip' => AdminIpWhitelistMiddleware::class,
        ]);

        $middleware->redirectGuestsTo(function () {
            session()->flash('info', 'Silakan masuk (login) atau mendaftar akun terlebih dahulu untuk melanjutkan proses checkout pesanan Anda.');

            return route('login');
        });

        // Pengecualian CSRF untuk endpoint callback Webhook Payment Gateway
        $middleware->validateCsrfTokens(except: [
            'payment/webhook',
            'payment/webhook/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
