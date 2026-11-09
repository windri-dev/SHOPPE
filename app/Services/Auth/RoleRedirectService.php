<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class RoleRedirectService
{
    /**
     * Tentukan URL atau rute tujuan pengalihan berdasarkan role pengguna.
     */
    public function getRedirectUrlForUser(?User $user = null): string
    {
        $user = $user ?? Auth::user();

        if (! $user) {
            return route('login');
        }

        if ($user->isAdmin()) {
            return route('admin.dashboard');
        }

        return route('account.index');
    }

    /**
     * Alihkan pengguna ke halaman yang sesuai dengan hak akses rolenya setelah login.
     *
     * - Admin -> Dashboard Admin (route: admin.dashboard)
     * - User / Customer -> Halaman Pengguna (route: account.index)
     */
    public function redirectBasedOnRole(?User $user = null, ?string $fallback = null): RedirectResponse
    {
        $user = $user ?? Auth::user();

        if ($user && $user->isAdmin()) {
            $defaultRoute = route('admin.dashboard');
            $intended = session()->pull('url.intended', $fallback ?: $defaultRoute);

            // Jangan biarkan admin di-redirect ke halaman akun pelanggan atau form login biasa
            if ($this->isStorefrontAuthUrl($intended)) {
                $intended = $defaultRoute;
            }

            return redirect()->to($intended)
                ->with('success', 'Login berhasil. Selamat datang di Panel Dashboard Admin!');
        }

        $defaultRoute = route('account.index');
        $intended = session()->pull('url.intended', $fallback ?: $defaultRoute);

        // Jangan biarkan non-admin di-redirect ke area admin internal
        if ($this->isAdminAreaUrl($intended)) {
            $intended = $defaultRoute;
        }

        return redirect()->to($intended)
            ->with('success', 'Login berhasil. Selamat datang di Halaman Akun Pengguna!');
    }

    /**
     * Cek apakah URL merupakan URL otentikasi/akun storefront.
     */
    protected function isStorefrontAuthUrl(string $url): bool
    {
        return str_contains($url, '/login') || str_contains($url, '/account');
    }

    /**
     * Cek apakah URL merupakan URL area administratif.
     */
    protected function isAdminAreaUrl(string $url): bool
    {
        return str_contains($url, '/admin');
    }
}
