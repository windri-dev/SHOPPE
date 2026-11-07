<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Store Administrator (Pengelola Toko Utama)
        User::updateOrCreate(
            ['email' => 'admin@shoppe.com'],
            [
                'name' => 'SHOPPE Store Manager',
                'password' => Hash::make('admin123'),
                'role' => UserRole::STORE_ADMIN,
                'phone' => '+62 812 3456 7890',
                'address' => 'SHOPPE Headquarters, Jakarta Pusat',
            ]
        );

        // 2. Explicit Admin Role User
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Master Administrator',
                'password' => Hash::make('admin123'),
                'role' => UserRole::ADMIN,
                'phone' => '+62 811 2233 4455',
                'address' => 'SHOPPE Office Tower Lt. 18, Jakarta',
            ]
        );

        // 3. Demo Customer (Pelanggan Umum)
        User::updateOrCreate(
            ['email' => 'buyer@shoppe.com'],
            [
                'name' => 'Clara Aurelia',
                'password' => Hash::make('buyer123'),
                'role' => UserRole::CUSTOMER,
                'phone' => '+62 898 7654 3210',
                'address' => 'Jl. Senopati No. 42, Kebayoran Baru, Jakarta Selatan 12190',
            ]
        );

        // 4. Explicit User Role User
        User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Regina Pratama',
                'password' => Hash::make('user123'),
                'role' => UserRole::USER,
                'phone' => '+62 856 7890 1234',
                'address' => 'Jl. Kemang Raya No. 10, Jakarta Selatan',
            ]
        );
    }
}
