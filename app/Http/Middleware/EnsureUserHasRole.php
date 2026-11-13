<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! auth()->check()) {
            return redirect()->guest(route('admin.login'))->with('error', 'Silakan login terlebih dahulu untuk mengakses panel pengelola toko.');
        }

        $user = auth()->user();
        $userRoleValue = $user->role instanceof UserRole ? $user->role->value : (string) $user->role;

        // Cek jika rute membutuhkan role administratif dan user adalah admin
        $isAdminRoleRequested = ! empty(array_intersect($roles, ['admin', 'store_admin', 'store_staff']));
        if ($isAdminRoleRequested && $user->isAdmin()) {
            return $next($request);
        }

        // Cek jika rute membutuhkan role pengguna umum dan user adalah customer/user
        $isUserRoleRequested = ! empty(array_intersect($roles, ['user', 'customer']));
        if ($isUserRoleRequested && $user->isUser()) {
            return $next($request);
        }

        if (! in_array($userRoleValue, $roles, true)) {
            abort(403, 'Akses Ditolak: Halaman ini hanya untuk Administrator dan Pengelola Toko PUTRIJALVA.');
        }

        return $next($request);
    }
}
