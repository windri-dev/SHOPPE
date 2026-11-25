<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\BankAccount;
use App\Models\StoreSetting;
use Illuminate\Database\Seeder;

class StoreSettingSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Bank Accounts
        $banks = [
            [
                'bank_name' => 'BCA',
                'bank_code' => 'bca',
                'account_number' => '123-456-7890',
                'account_holder' => 'PT PUTRIJALVA INDONESIA',
                'branch' => 'KCU Sudirman Jakarta',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'bank_name' => 'Bank Mandiri',
                'bank_code' => 'mandiri',
                'account_number' => '102-00-1234567-8',
                'account_holder' => 'PT PUTRIJALVA INDONESIA',
                'branch' => 'KCP Plaza Senayan',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'bank_name' => 'Bank BNI',
                'bank_code' => 'bni',
                'account_number' => '987-654-3210',
                'account_holder' => 'PT PUTRIJALVA INDONESIA',
                'branch' => 'KC Senopati',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'bank_name' => 'Bank BRI',
                'bank_code' => 'bri',
                'account_number' => '0123-01-001234-56-7',
                'account_holder' => 'PT PUTRIJALVA INDONESIA',
                'branch' => 'KC Jakarta Gatot Subroto',
                'is_active' => false,
                'sort_order' => 4,
            ],
            [
                'bank_name' => 'SeaBank',
                'bank_code' => 'seabank',
                'account_number' => '9012-3456-7890',
                'account_holder' => 'PT PUTRIJALVA INDONESIA',
                'branch' => 'Digital Banking Jakarta',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'bank_name' => 'Bank Syariah Indonesia (BSI)',
                'bank_code' => 'bsi',
                'account_number' => '711-2233-445',
                'account_holder' => 'PT PUTRIJALVA INDONESIA',
                'branch' => 'KC Jakarta Hasanuddin',
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'bank_name' => 'CIMB Niaga',
                'bank_code' => 'cimb',
                'account_number' => '800-123-4567-00',
                'account_holder' => 'PT PUTRIJALVA INDONESIA',
                'branch' => 'KC Sudirman',
                'is_active' => true,
                'sort_order' => 7,
            ],
            [
                'bank_name' => 'Bank Permata',
                'bank_code' => 'permata',
                'account_number' => '410-987-6543',
                'account_holder' => 'PT PUTRIJALVA INDONESIA',
                'branch' => 'KC Senayan City',
                'is_active' => true,
                'sort_order' => 8,
            ],
            [
                'bank_name' => 'Bank Danamon',
                'bank_code' => 'danamon',
                'account_number' => '358-123-4567',
                'account_holder' => 'PT PUTRIJALVA INDONESIA',
                'branch' => 'KC Kuningan',
                'is_active' => true,
                'sort_order' => 9,
            ],
            [
                'bank_name' => 'Bank Jago',
                'bank_code' => 'jago',
                'account_number' => '100-2345-6789',
                'account_holder' => 'PT PUTRIJALVA INDONESIA',
                'branch' => 'Digital Banking',
                'is_active' => true,
                'sort_order' => 10,
            ],
            [
                'bank_name' => 'Bank BTN',
                'bank_code' => 'btn',
                'account_number' => '001-22-33-44556-7',
                'account_holder' => 'PT PUTRIJALVA INDONESIA',
                'branch' => 'KC Jakarta Harmoni',
                'is_active' => true,
                'sort_order' => 11,
            ],
            [
                'bank_name' => 'Jenius (BTPN)',
                'bank_code' => 'jenius',
                'account_number' => '900-123-45678',
                'account_holder' => 'PT PUTRIJALVA INDONESIA',
                'branch' => 'SMBC BTPN Digital',
                'is_active' => true,
                'sort_order' => 12,
            ],
        ];

        foreach ($banks as $bank) {
            BankAccount::updateOrCreate(
                ['account_number' => $bank['account_number']],
                $bank
            );
        }

        // 2. Store Social Media & Contacts
        $settings = [
            ['key' => 'social_instagram', 'value' => 'https://instagram.com/putrijalva.official', 'group' => 'social'],
            ['key' => 'social_tiktok', 'value' => 'https://tiktok.com/@putrijalva', 'group' => 'social'],
            ['key' => 'social_whatsapp', 'value' => '6281234567890', 'group' => 'social'],
            ['key' => 'social_facebook', 'value' => 'https://facebook.com/putrijalva', 'group' => 'social'],
            ['key' => 'social_youtube', 'value' => 'https://youtube.com/@putrijalva', 'group' => 'social'],
            ['key' => 'store_email', 'value' => 'concierge@wn-cdn.net', 'group' => 'contact'],
            ['key' => 'store_phone', 'value' => '+62 812-3456-7890', 'group' => 'contact'],
            ['key' => 'store_address', 'value' => 'PUTRIJALVA Atelier Studio, Jl. Senopati No. 42, Kebayoran Baru, Jakarta Selatan', 'group' => 'contact'],
        ];

        foreach ($settings as $s) {
            StoreSetting::set($s['key'], $s['value'], $s['group']);
        }
    }
}
