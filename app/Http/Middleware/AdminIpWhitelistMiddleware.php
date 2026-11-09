<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\IpUtils;
use Symfony\Component\HttpFoundation\Response;

class AdminIpWhitelistMiddleware
{
    /**
     * Memeriksa apakah IP klien terdaftar dalam whitelist admin.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var list<string> $whitelist */
        $whitelist = config('admin.ip_whitelist', []);
        $alwaysAllowLocal = (bool) config('admin.always_allow_local', true);

        // Jika whitelist belum diisi atau berisi '*', maka batasan dinonaktifkan
        if (empty($whitelist) || in_array('*', $whitelist, true)) {
            return $next($request);
        }

        $clientIp = (string) $request->ip();

        // Selalu izinkan loopback localhost jika always_allow_local aktif
        if ($alwaysAllowLocal && in_array($clientIp, ['127.0.0.1', '::1'], true)) {
            return $next($request);
        }

        // Cek apakah IP klien sesuai dengan daftar whitelist (mendukung IPv4, IPv6, dan CIDR)
        if (IpUtils::checkIp($clientIp, $whitelist)) {
            return $next($request);
        }

        Log::warning('Percobaan akses panel admin diblokir: IP tidak terdaftar dalam whitelist', [
            'client_ip' => $clientIp,
            'url' => $request->fullUrl(),
            'user_agent' => $request->userAgent(),
        ]);

        abort(403, sprintf('Akses Ditolak: Alamat IP Anda (%s) tidak terdaftar dalam whitelist pengelola toko.', $clientIp));
    }
}
