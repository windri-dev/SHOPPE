<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AiKnowledgeEntry;
use Illuminate\Database\Seeder;

class AiKnowledgeSeeder extends Seeder
{
    public function run(): void
    {
        $entries = [
            // 1. Knowledge Base (Pengetahuan Toko & Layanan)
            [
                'type' => 'knowledge',
                'title' => 'Material & Keaslian Perhiasan',
                'keywords' => 'bahan, material, emas, karat, vermeil, perak, silver, alergi, gatal, hypoallergenic, luntur, karat',
                'content' => 'Seluruh koleksi perhiasan PUTRI.J dibuat menggunakan bahan Premium Brass & 925 Sterling Silver dengan lapisan 18K Gold Vermeil (ketebalan 2.5 mikron). 100% bebas nikel dan timbal (Hypoallergenic) sehingga aman untuk kulit sensitif dan tidak mudah pudar.',
                'requires_human_handoff' => false,
                'is_active' => true,
            ],
            [
                'type' => 'knowledge',
                'title' => 'Metode Pembayaran & Verifikasi',
                'keywords' => 'bayar, pembayaran, rekening, bca, mandiri, qris, transfer, konfirmasi, kartu kredit',
                'content' => 'PUTRI.J menerima pembayaran melalui Transfer Bank Resmi (BCA 123-456-7890 & Mandiri 102-00-1234567-8 a.n. PT Putri.J Luxury Indonesia), QRIS Instant Payment, dan Kartu Kredit/Debit. Pelanggan dapat mengunggah konfirmasi transfer manual atau menggunakan Simulasi Bayar Instan pada halaman Detail Pesanan.',
                'requires_human_handoff' => false,
                'is_active' => true,
            ],
            [
                'type' => 'knowledge',
                'title' => 'Pengiriman, Asuransi & Gratis Ongkir',
                'keywords' => 'ongkir, kirim, pengiriman, ekspedisi, jne, sicepat, asuransi, lama pengiriman, gratis ongkir',
                'content' => 'Kami menyediakan Gratis Ongkos Kirim ke seluruh Indonesia untuk pesanan di atas $50.00. Pesanan yang lunas sebelum pukul 16.00 WIB dikirim pada hari yang sama dengan perlindungan asuransi penuh dan kemasan velvet box bersegel.',
                'requires_human_handoff' => false,
                'is_active' => true,
            ],
            [
                'type' => 'knowledge',
                'title' => 'Ukuran Cincin & Rantai Kalung',
                'keywords' => 'ukuran, size, lingkar jari, panjang kalung, rantai, extender, berat anting',
                'content' => 'Kalung PUTRI.J memiliki panjang rantai standar 40cm + 5cm extender. Cincin tersedia dalam ukuran US 5, 6, 7, dan 8. Anting dirancang ultra-ringan (di bawah 8 gram per pasang) agar nyaman dipakai seharian.',
                'requires_human_handoff' => false,
                'is_active' => true,
            ],

            // 2. Boundaries / Guardrails (Batasan AI & Eskalasi ke Manusia)
            [
                'type' => 'boundary',
                'title' => 'Batasan Komplain, Barang Rusak & Refund Dana',
                'keywords' => 'refund, kembalikan uang, komplain, rusak, patah, cacat, kecewa, barang kurang, salah kirim, batal, cancel',
                'content' => 'Untuk pengajuan komplain, klaim garansi barang rusak/cacat, pembatalan pesanan, atau pengembalian dana (refund), AI tidak diizinkan mengambil keputusan sepihak. Percakapan Anda langsung kami alihkan ke Admin Customer Care (Manusia) agar ditangani secara prioritas.',
                'requires_human_handoff' => true,
                'is_active' => true,
            ],
            [
                'type' => 'boundary',
                'title' => 'Batasan Pesanan Custom / Ukir Nama & Grosir B2B',
                'keywords' => 'custom, ukir nama, grafir, engrave, grosir, reseller, partai besar, korporat, souvenir',
                'content' => 'Permintaan grafir/ukir inisial khusus (custom engraving) dan pemesanan korporat/grosir membutuhkan pengecekan jadwal pengrajin secara manual. Kami mengalihkan obrolan ini kepada Staf Concierge kami untuk membantu Anda.',
                'requires_human_handoff' => true,
                'is_active' => true,
            ],
            [
                'type' => 'boundary',
                'title' => 'Batasan Negosiasi Harga & Diskon Tidak Resmi',
                'keywords' => 'nego, tawar, kurangin harga, minta diskon khusus, harga teman, diskon 90%',
                'content' => 'Mohon maaf, sebagai asisten AI resmi PUTRI.J saya memiliki batasan untuk tidak memberikan potongan harga atau negosiasi di luar harga resmi yang tertera pada katalog toko. Anda tetap dapat menikmati harga promo resmi pada produk berlabel Discount di halaman Shop.',
                'requires_human_handoff' => false,
                'is_active' => true,
            ],
            [
                'type' => 'boundary',
                'title' => 'Batasan Topik Di Luar Layanan PUTRI.J',
                'keywords' => 'politik, presiden, pemilu, judi, pinjol, kripto, resep masakan, tugas sekolah, obat medis',
                'content' => 'Mohon maaf, Aria AI Concierge memiliki batasan pengetahuan khusus untuk melayani konsultasi produk perhiasan, pengecekan harga & stok katalog, serta bantuan pesanan di PUTRI.J Luxury.',
                'requires_human_handoff' => false,
                'is_active' => true,
            ],

            // 3. Response Rules (Aturan Respons & Gaya Komunikasi Pelanggan)
            [
                'type' => 'response_rule',
                'title' => 'Standar Sapaan & Transparansi Stok/Harga',
                'keywords' => 'halo, hai, pagi, siang, sore, malam, permisi, tanya',
                'content' => 'Selalu sapa pelanggan dengan sopan, ramah, dan gunakan data harga ($) serta jumlah stok aktual dari database katalog. Jika pelanggan meminta berbicara dengan manusia/admin, segera alihkan status percakapan ke mode Admin Manusia.',
                'requires_human_handoff' => false,
                'is_active' => true,
            ],
            [
                'type' => 'response_rule',
                'title' => 'Panduan Tukar Ukuran (Size Exchange 7 Hari)',
                'keywords' => 'tukar ukuran, kebesaran, kekecilan, resize, retur ukuran',
                'content' => 'Pelanggan berhak menukar ukuran cincin/gelang secara gratis 1 kali dalam 7 hari sejak pesanan diterima (selama segel utuh dan belum dipakai). Arahkan pelanggan untuk menyebutkan nomor pesanan atau hubungkan ke Admin jika ingin memproses penukaran.',
                'requires_human_handoff' => false,
                'is_active' => true,
            ],
        ];

        AiKnowledgeEntry::where('title', 'like', '%SHOPPE%')->delete();

        foreach ($entries as $entry) {
            AiKnowledgeEntry::updateOrCreate(['title' => $entry['title']], $entry);
        }
    }
}

