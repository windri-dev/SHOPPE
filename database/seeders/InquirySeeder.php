<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\InquiryStatus;
use App\Models\Inquiry;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class InquirySeeder extends Seeder
{
    public function run(): void
    {
        $clara = User::where('email', 'buyer@shoppe.com')->first();
        $regina = User::where('email', 'user@example.com')->first();
        $dimas = User::where('email', 'dimas.satria@example.com')->first();
        $amanda = User::where('email', 'amanda.kusuma@example.com')->first();

        $inquiriesData = [
            // 1. UNREAD - Livechat dari Clara Aurelia
            [
                'ticket_number' => 'TCK-2026-001',
                'user' => $clara,
                'name' => 'Clara Aurelia',
                'email' => 'buyer@shoppe.com',
                'phone' => '+62 898 7654 3210',
                'subject' => 'Restock Anting Hal Double Loop',
                'channel' => 'livechat',
                'status' => InquiryStatus::UNREAD,
                'created_at' => Carbon::now()->subHours(2),
                'messages' => [
                    [
                        'sender_type' => 'customer',
                        'message' => 'Halo min, apakah anting Hal Double Loop akan ada restock batch baru minggu depan? Saya butuh untuk kado wisuda sahabat.',
                        'created_at' => Carbon::now()->subHours(2),
                    ],
                ],
            ],

            // 2. UNREAD - Contact Form dari Dimas Satria
            [
                'ticket_number' => 'TCK-2026-002',
                'user' => $dimas,
                'name' => 'Dimas Satria',
                'email' => 'dimas.satria@example.com',
                'phone' => '+62 812 8877 6655',
                'subject' => 'Ukuran Cincin Atelier Oval Signet',
                'channel' => 'contact_form',
                'status' => InquiryStatus::UNREAD,
                'created_at' => Carbon::now()->subHours(5),
                'messages' => [
                    [
                        'sender_type' => 'customer',
                        'message' => 'Selamat siang tim PUTRI.J, keliling jari saya 56mm. Untuk cincin Atelier Oval Signet lebih disarankan size 6 atau 7 ya?',
                        'created_at' => Carbon::now()->subHours(5),
                    ],
                ],
            ],

            // 3. UNREAD - Livechat dari Tamu (Guest)
            [
                'ticket_number' => 'TCK-2026-003',
                'user' => null,
                'name' => 'Jessica Tanuwijaya',
                'email' => 'jessica.tan@gmail.com',
                'phone' => '+62 811 9988 2211',
                'subject' => 'Apakah Bahan Perhiasan Aman untuk Kulit Sensitif?',
                'channel' => 'livechat',
                'status' => InquiryStatus::UNREAD,
                'created_at' => Carbon::now()->subHours(9),
                'messages' => [
                    [
                        'sender_type' => 'customer',
                        'message' => 'Halo, telinga saya sangat sensitif terhadap nikel dan gampang radang. Apakah material Solace Baroque Pearl benar-benar 100% hypoallergenic?',
                        'created_at' => Carbon::now()->subHours(9),
                    ],
                ],
            ],

            // 4. UNREAD - Contact Form dari Guest
            [
                'ticket_number' => 'TCK-2026-004',
                'user' => null,
                'name' => 'Rangga Pratama',
                'email' => 'rangga.p@yahoo.com',
                'phone' => '+62 813 4455 6677',
                'subject' => 'Pengiriman Same Day Jakarta',
                'channel' => 'contact_form',
                'status' => InquiryStatus::UNREAD,
                'created_at' => Carbon::now()->subHours(14),
                'messages' => [
                    [
                        'sender_type' => 'customer',
                        'message' => 'Apakah memungkinkan pengiriman pakai kurir instant GoSend/GrabExpress untuk pesanan area Kuningan Jakarta Selatan?',
                        'created_at' => Carbon::now()->subHours(14),
                    ],
                ],
            ],

            // 5. RESPONDED - Livechat dari Regina Pratama
            [
                'ticket_number' => 'TCK-2026-005',
                'user' => $regina,
                'name' => 'Regina Pratama',
                'email' => 'user@example.com',
                'phone' => '+62 856 7890 1234',
                'subject' => 'Pelapisan Ulang (Re-plating) Gelang Paloma',
                'channel' => 'livechat',
                'status' => InquiryStatus::RESPONDED,
                'created_at' => Carbon::now()->subDays(1),
                'messages' => [
                    [
                        'sender_type' => 'customer',
                        'message' => 'Halo, apakah PUTRI.J menyediakan layanan re-plating emas jika gelang saya nanti mulai kusam setelah dipakai bertahun-tahun?',
                        'created_at' => Carbon::now()->subDays(1)->addMinutes(10),
                    ],
                    [
                        'sender_type' => 'admin',
                        'message' => 'Halo Kak Regina! Ya, kami menyediakan layanan Lifetime Care Service termasuk pembersihan ultrasonik gratis dan jasa replating 18k gold dengan biaya terjangkau.',
                        'created_at' => Carbon::now()->subDays(1)->addMinutes(35),
                    ],
                ],
            ],

            // 6. RESPONDED - Contact Form dari Amanda Kusuma
            [
                'ticket_number' => 'TCK-2026-006',
                'user' => $amanda,
                'name' => 'Amanda Kusuma',
                'email' => 'amanda.kusuma@example.com',
                'phone' => '+62 878 5544 3322',
                'subject' => 'Request Custom Gift Box & Pita Hijau Zamrud',
                'channel' => 'contact_form',
                'status' => InquiryStatus::RESPONDED,
                'created_at' => Carbon::now()->subDays(2),
                'messages' => [
                    [
                        'sender_type' => 'customer',
                        'message' => 'Apakah pesanan Signature Solstice Set saya bisa diikat pita hijau zamrud dengan lilin segel wax stamp?',
                        'created_at' => Carbon::now()->subDays(2),
                    ],
                    [
                        'sender_type' => 'admin',
                        'message' => 'Tentu saja Kak Amanda, tim concierge kami dengan senang hati mengemasnya dengan wax seal dan pita hijau signature PUTRI.J tanpa biaya tambahan.',
                        'created_at' => Carbon::now()->subDays(2)->addHours(2),
                    ],
                ],
            ],

            // 7. RESPONDED - Livechat dari Guest
            [
                'ticket_number' => 'TCK-2026-007',
                'user' => null,
                'name' => 'Michael Salim',
                'email' => 'michaelsalim@outlook.com',
                'phone' => '+62 817 2233 4455',
                'subject' => 'Garansi Jam Tangan Chrono Minimalist',
                'channel' => 'livechat',
                'status' => InquiryStatus::RESPONDED,
                'created_at' => Carbon::now()->subDays(3),
                'messages' => [
                    [
                        'sender_type' => 'customer',
                        'message' => 'Berapa lama garansi mesin jam tangan Chrono Milanese Watch? Apakah mencakup penggantian baterai?',
                        'created_at' => Carbon::now()->subDays(3),
                    ],
                    [
                        'sender_type' => 'admin',
                        'message' => 'Selamat siang Pak Michael, seluruh jam tangan artisan PUTRI.J dilengkapi kartu garansi resmi mesin 2 tahun dan gratis ganti baterai 1x di studio kami.',
                        'created_at' => Carbon::now()->subDays(3)->addMinutes(20),
                    ],
                ],
            ],

            // 8. RESOLVED - Livechat dari Clara Aurelia
            [
                'ticket_number' => 'TCK-2026-008',
                'user' => $clara,
                'name' => 'Clara Aurelia',
                'email' => 'buyer@shoppe.com',
                'phone' => '+62 898 7654 3210',
                'subject' => 'Update Nomor Resi Pesanan SHP-2026-1001',
                'channel' => 'livechat',
                'status' => InquiryStatus::RESOLVED,
                'created_at' => Carbon::now()->subDays(24),
                'messages' => [
                    [
                        'sender_type' => 'customer',
                        'message' => 'Halo admin, apakah pesanan saya sudah diserahkan ke JNE? Belum ada status tracking di halaman akun.',
                        'created_at' => Carbon::now()->subDays(24),
                    ],
                    [
                        'sender_type' => 'admin',
                        'message' => 'Halo Kak Clara! Paket sudah dijemput kurir sore ini dengan nomor resi JNE-8829103948. Data tracking sudah diperbarui di dashboard Anda.',
                        'created_at' => Carbon::now()->subDays(24)->addMinutes(15),
                    ],
                    [
                        'sender_type' => 'customer',
                        'message' => 'Terima kasih banyak atas update kilatnya min! Sangat membantu.',
                        'created_at' => Carbon::now()->subDays(24)->addMinutes(22),
                    ],
                    [
                        'sender_type' => 'admin',
                        'message' => 'Sama-sama Kak Clara, senang bisa melayani Anda. Selamat menikmati perhiasan cantiknya!',
                        'created_at' => Carbon::now()->subDays(24)->addMinutes(25),
                    ],
                ],
            ],

            // 9. RESOLVED - Contact Form dari Dimas Satria
            [
                'ticket_number' => 'TCK-2026-009',
                'user' => $dimas,
                'name' => 'Dimas Satria',
                'email' => 'dimas.satria@example.com',
                'phone' => '+62 812 8877 6655',
                'subject' => 'Tukar Size Cincin Dome Ring',
                'channel' => 'contact_form',
                'status' => InquiryStatus::RESOLVED,
                'created_at' => Carbon::now()->subDays(15),
                'messages' => [
                    [
                        'sender_type' => 'customer',
                        'message' => 'Paket cincin sudah sampai, tapi ternyata agak longgar di jari manis istri saya. Bisakah ditukar ke ukuran lebih kecil?',
                        'created_at' => Carbon::now()->subDays(15),
                    ],
                    [
                        'sender_type' => 'admin',
                        'message' => 'Tentu bisa Kak Dimas. Kebijakan PUTRI.J memungkinkan pertukaran size gratis dalam 7 hari setelah barang diterima, asalkan tag segel masih utuh.',
                        'created_at' => Carbon::now()->subDays(15)->addHours(1),
                    ],
                    [
                        'sender_type' => 'customer',
                        'message' => 'Segel masih lengkap. Saya kirim balik hari ini ya min ke alamat studio.',
                        'created_at' => Carbon::now()->subDays(15)->addHours(2),
                    ],
                    [
                        'sender_type' => 'admin',
                        'message' => 'Baik Kak, cincin pengganti size 6 sudah kami siapkan dan langsung kami kirimkan begitu paket retur tiba.',
                        'created_at' => Carbon::now()->subDays(15)->addHours(3),
                    ],
                ],
            ],

            // 10. RESOLVED - Livechat dari Guest
            [
                'ticket_number' => 'TCK-2026-010',
                'user' => null,
                'name' => 'Putri Wulandari',
                'email' => 'putri.wulan@gmail.com',
                'phone' => '+62 822 3344 5566',
                'subject' => 'Panduan Perawatan Jepit Kaede',
                'channel' => 'livechat',
                'status' => InquiryStatus::RESOLVED,
                'created_at' => Carbon::now()->subDays(9),
                'messages' => [
                    [
                        'sender_type' => 'customer',
                        'message' => 'Bagaimana cara membersihkan debu pada tekstur satin jepit rambut Kaede?',
                        'created_at' => Carbon::now()->subDays(9),
                    ],
                    [
                        'sender_type' => 'admin',
                        'message' => 'Halo Kak Putri! Cukup gunakan sikat berbulu sangat halus (seperti kuas makeup bersih) dan lap mikrofiber kering bawaan produk.',
                        'created_at' => Carbon::now()->subDays(9)->addMinutes(12),
                    ],
                ],
            ],
        ];

        foreach ($inquiriesData as $data) {
            $user = $data['user'];

            $inquiry = Inquiry::updateOrCreate(
                ['ticket_number' => $data['ticket_number']],
                [
                    'user_id' => $user?->id,
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'subject' => $data['subject'],
                    'channel' => $data['channel'],
                    'status' => $data['status'],
                    'created_at' => $data['created_at'],
                    'updated_at' => $data['created_at'],
                ]
            );

            $inquiry->messages()->delete();

            foreach ($data['messages'] as $msg) {
                $inquiry->messages()->create([
                    'sender_type' => $msg['sender_type'],
                    'message' => $msg['message'],
                    'created_at' => $msg['created_at'],
                ]);
            }
        }
    }
}
