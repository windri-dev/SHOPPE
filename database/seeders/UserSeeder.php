<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            // 1. Store Administrator (Pengelola Toko Utama)
            [
                'email' => 'admin@shoppe.com',
                'name' => 'PUTRI.J Store Manager',
                'username' => 'shoppe.manager',
                'password' => Hash::make('admin123'),
                'role' => UserRole::STORE_ADMIN,
                'phone' => '+62 812 3456 7890',
                'gender' => 'Laki-laki',
                'birth_date' => '1990-05-14',
                'address' => 'PUTRI.J Headquarters, Jl. Sudirman Kav. 52',
                'country' => 'Indonesia',
                'province' => 'DKI Jakarta',
                'city' => 'Jakarta Pusat',
                'district' => 'Tanah Abang',
                'postcode' => '10210',
                'address_label' => 'Kantor',
                'address_notes' => 'Lobby Utama Lantai Dasar',
            ],
            // 2. Explicit Admin Role User
            [
                'email' => 'admin@example.com',
                'name' => 'Master Administrator',
                'username' => 'master.admin',
                'password' => Hash::make('admin123'),
                'role' => UserRole::ADMIN,
                'phone' => '+62 811 2233 4455',
                'gender' => 'Laki-laki',
                'birth_date' => '1988-11-20',
                'address' => 'PUTRI.J Office Tower Lt. 18, Mega Kuningan',
                'country' => 'Indonesia',
                'province' => 'DKI Jakarta',
                'city' => 'Jakarta Selatan',
                'district' => 'Setiabudi',
                'postcode' => '12950',
                'address_label' => 'Kantor',
            ],
            // 3. Store Staff / Concierge Operator
            [
                'email' => 'staff@shoppe.com',
                'name' => 'Budi Santoso (Customer Care)',
                'username' => 'budi.concierge',
                'password' => Hash::make('staff123'),
                'role' => UserRole::STORE_STAFF,
                'phone' => '+62 813 9988 7766',
                'gender' => 'Laki-laki',
                'birth_date' => '1995-03-10',
                'address' => 'Jl. Gatot Subroto No. 88',
                'country' => 'Indonesia',
                'province' => 'DKI Jakarta',
                'city' => 'Jakarta Selatan',
                'district' => 'Mampang Prapatan',
                'postcode' => '12710',
                'address_label' => 'Kantor',
            ],
            // 4. Demo Customer 1 (Clara Aurelia - Active VIP Buyer)
            [
                'email' => 'buyer@shoppe.com',
                'name' => 'Clara Aurelia',
                'username' => 'clara.aurelia',
                'password' => Hash::make('buyer123'),
                'role' => UserRole::CUSTOMER,
                'phone' => '089876543210',
                'gender' => 'Perempuan',
                'birth_date' => '1997-08-24',
                'address' => 'Jl. Senopati No. 42, RT 04/RW 02',
                'country' => 'Indonesia',
                'province' => 'DKI Jakarta',
                'city' => 'Jakarta Selatan',
                'district' => 'Kebayoran Baru',
                'postcode' => '12190',
                'address_label' => 'Rumah',
                'address_notes' => 'Rumah pagar hitam sebelah butik kopi',
                'saved_addresses' => [
                    [
                        'id' => 'addr-clara-1',
                        'label' => 'Rumah',
                        'recipient_name' => 'Clara Aurelia',
                        'phone' => '089876543210',
                        'country' => 'Indonesia',
                        'province' => 'DKI Jakarta',
                        'city' => 'Jakarta Selatan',
                        'district' => 'Kebayoran Baru',
                        'postcode' => '12190',
                        'street_address' => 'Jl. Senopati No. 42, RT 04/RW 02',
                        'notes' => 'Rumah pagar hitam sebelah butik kopi',
                        'is_primary' => true,
                    ],
                    [
                        'id' => 'addr-clara-2',
                        'label' => 'Kantor',
                        'recipient_name' => 'Clara Aurelia (Pacific Place)',
                        'phone' => '089876543210',
                        'country' => 'Indonesia',
                        'province' => 'DKI Jakarta',
                        'city' => 'Jakarta Selatan',
                        'district' => 'Kebayoran Baru',
                        'postcode' => '12190',
                        'street_address' => 'One Pacific Place Tower Lt. 15, SCBD Jl. Jend. Sudirman Kav. 52-53',
                        'notes' => 'Titip di resepsionis lantai 15 pada jam kerja',
                        'is_primary' => false,
                    ],
                ],
                'bank_account' => [
                    'bank_name' => 'BCA',
                    'account_number' => '5240981234',
                    'account_holder' => 'Clara Aurelia',
                ],
                'notification_preferences' => [
                    'email_order' => true,
                    'whatsapp_shipping' => true,
                    'promo_newsletter' => true,
                ],
            ],
            // 5. Demo Customer 2 (Regina Pratama - Regular User)
            [
                'email' => 'user@example.com',
                'name' => 'Regina Pratama',
                'username' => 'regina.pratama',
                'password' => Hash::make('user123'),
                'role' => UserRole::USER,
                'phone' => '085678901234',
                'gender' => 'Perempuan',
                'birth_date' => '1999-02-18',
                'address' => 'Jl. Kemang Raya No. 10',
                'country' => 'Indonesia',
                'province' => 'DKI Jakarta',
                'city' => 'Jakarta Selatan',
                'district' => 'Mampang Prapatan',
                'postcode' => '12730',
                'address_label' => 'Rumah',
                'address_notes' => 'Komplek Kemang Pratama Blok B2',
                'bank_account' => [
                    'bank_name' => 'Mandiri',
                    'account_number' => '1240009988776',
                    'account_holder' => 'Regina Pratama',
                ],
            ],
            // 6. Customer 3 (Dimas Satria)
            [
                'email' => 'dimas.satria@example.com',
                'name' => 'Dimas Satria',
                'username' => 'dimas.satria',
                'password' => Hash::make('password123'),
                'role' => UserRole::CUSTOMER,
                'phone' => '081288776655',
                'gender' => 'Laki-laki',
                'birth_date' => '1994-06-12',
                'address' => 'Jl. Dago Asri No. 15',
                'country' => 'Indonesia',
                'province' => 'Jawa Barat',
                'city' => 'Kota Bandung',
                'district' => 'Coblong',
                'postcode' => '40135',
                'address_label' => 'Rumah',
            ],
            // 7. Customer 4 (Amanda Kusuma)
            [
                'email' => 'amanda.kusuma@example.com',
                'name' => 'Amanda Kusuma',
                'username' => 'amanda.kusuma',
                'password' => Hash::make('password123'),
                'role' => UserRole::CUSTOMER,
                'phone' => '087855443322',
                'gender' => 'Perempuan',
                'birth_date' => '1996-12-05',
                'address' => 'Jl. Raya Darmo Permai III No. 8',
                'country' => 'Indonesia',
                'province' => 'Jawa Timur',
                'city' => 'Kota Surabaya',
                'district' => 'Sukomanunggal',
                'postcode' => '60189',
                'address_label' => 'Rumah',
            ],
            // 8. Customer 5 (Sofia Maharani)
            [
                'email' => 'sofia.maharani@example.com',
                'name' => 'Sofia Maharani',
                'username' => 'sofia.maharani',
                'password' => Hash::make('password123'),
                'role' => UserRole::CUSTOMER,
                'phone' => '081911223344',
                'gender' => 'Perempuan',
                'birth_date' => '1998-04-29',
                'address' => 'Jl. Sunset Road No. 108, Seminyak',
                'country' => 'Indonesia',
                'province' => 'Bali',
                'city' => 'Kab. Badung',
                'district' => 'Kuta',
                'postcode' => '80361',
                'address_label' => 'Rumah',
            ],
            // 9. Customer 6 (Kevin Wijaya)
            [
                'email' => 'kevin.wijaya@example.com',
                'name' => 'Kevin Wijaya',
                'username' => 'kevin.wijaya',
                'password' => Hash::make('password123'),
                'role' => UserRole::CUSTOMER,
                'phone' => '082166554433',
                'gender' => 'Laki-laki',
                'birth_date' => '1993-09-15',
                'address' => 'Jl. Diponegoro No. 24',
                'country' => 'Indonesia',
                'province' => 'Sumatera Utara',
                'city' => 'Kota Medan',
                'district' => 'Medan Baru',
                'postcode' => '20152',
                'address_label' => 'Kantor',
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(['email' => $userData['email']], $userData);
        }
    }
}
