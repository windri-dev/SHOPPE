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
