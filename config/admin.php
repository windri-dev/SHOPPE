<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Admin IP Whitelist
    |--------------------------------------------------------------------------
    |
    | Daftar alamat IP yang diizinkan untuk mengakses dashboard dan panel admin.
    | Jika kosong atau tidak diset, maka whitelist nonaktif (semua IP diizinkan).
    | Mendukung IP spesifik (IPv4 / IPv6) dan subnet CIDR (contoh: 192.168.1.0/24).
    | Konfigurasi dipisahkan koma pada .env:
    | ADMIN_IP_WHITELIST=127.0.0.1,::1,192.168.1.100
    |
    */
    'ip_whitelist' => array_values(array_filter(
        array_map('trim', explode(',', (string) env('ADMIN_IP_WHITELIST', ''))),
        fn (string $ip): bool => $ip !== ''
    )),

    /*
    |--------------------------------------------------------------------------
    | Always Allow Localhost
    |--------------------------------------------------------------------------
    |
    | Jika true, IP loopback lokal (127.0.0.1 dan ::1) akan selalu diizinkan
    | untuk mencegah developer terkunci saat pengembangan lokal.
    |
    */
    'always_allow_local' => (bool) env('ADMIN_IP_ALWAYS_ALLOW_LOCAL', true),
];
