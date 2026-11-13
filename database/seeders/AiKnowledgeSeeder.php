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
                'title' => 'Material Kain & Kualitas Jahitan',
                'keywords' => 'bahan, material, kain, linen, silk, sutra, katun, kasmir, panas, adem, berbulu, tebal, tipis, furing',
                'content' => 'Seluruh koleksi pakaian & baju crop top PUTRIJALVA merupakan produk asli Indonesia yang dirancang menggunakan material kain premium pilihan seperti Linen Alami berkualitas tinggi yang breathable, Mulberry Silk Blend yang lembut berkilau elegan, dan Katun Organik berserat rapat. Jahitan berstandar butik dengan finishing rapi yang nyaman di kulit.',
                'requires_human_handoff' => false,
                'is_active' => true,
            ],
            [
                'type' => 'knowledge',
                'title' => 'Metode Pembayaran & Verifikasi',
                'keywords' => 'bayar, pembayaran, rekening, bca, mandiri, qris, transfer, konfirmasi, kartu kredit',
                'content' => 'PUTRIJALVA menerima pembayaran melalui Transfer Bank Resmi (BCA 123-456-7890 & Mandiri 102-00-1234567-8 a.n. PT PUTRIJALVA INDONESIA), QRIS Instant Payment, dan Kartu Kredit/Debit. Pelanggan dapat mengunggah konfirmasi transfer manual atau menggunakan Simulasi Bayar Instan pada halaman Detail Pesanan.',
                'requires_human_handoff' => false,
                'is_active' => true,
            ],
            [
                'type' => 'knowledge',
                'title' => 'Pengiriman, Asuransi & Gratis Ongkir',
                'keywords' => 'ongkir, kirim, pengiriman, ekspedisi, jne, sicepat, asuransi, lama pengiriman, gratis ongkir',
                'content' => 'Kami menyediakan Gratis Ongkos Kirim ke seluruh Indonesia untuk pesanan di atas $50.00. Pesanan yang lunas sebelum pukul 16.00 WIB dikirim pada hari yang sama dengan perlindungan asuransi penuh dan kemasan garment wrap bersegel aman.',
                'requires_human_handoff' => false,
                'is_active' => true,
            ],
            [
                'type' => 'knowledge',
                'title' => 'Panduan Ukuran (Size Guide) & Garansi Retur 1 Hari',
                'keywords' => 'panduan ukuran, size chart, lingkar dada, panjang baju, ukuran s, ukuran m, ukuran l, ukuran xl, size s, size m, size l, size xl, all size, retur, rekomendasi ukuran',
                'content' => 'Pakaian atasan crop top PUTRIJALVA tersedia dalam ukuran S (LD 88-92 cm), M (LD 94-98 cm), L (LD 100-104 cm), dan XL (LD 106-112 cm), serta varian All Size untuk item aksesoris. Kami menyediakan garansi retur jika barang rusak atau tidak sesuai pesanan maksimal 1 hari setelah diterima.',
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
                'title' => 'Batasan Pesanan Custom / Jahit Khusus & Grosir B2B',
                'keywords' => 'custom, jahit khusus, seragam, pesan banyak, grosir, reseller, partai besar, korporat, souvenir',
                'content' => 'Permintaan pesanan jahit custom khusus, seragam, atau pemesanan grosir/partai besar membutuhkan koordinasi produksi secara manual. Kami mengalihkan obrolan ini kepada Staf Concierge kami untuk membantu Anda secara personal.',
                'requires_human_handoff' => true,
                'is_active' => true,
            ],
            [
                'type' => 'boundary',
                'title' => 'Batasan Negosiasi Harga & Diskon Tidak Resmi',
                'keywords' => 'nego, tawar, kurangin harga, minta diskon khusus, harga teman, diskon 90%',
                'content' => 'Mohon maaf, sebagai asisten AI resmi PUTRIJALVA saya memiliki batasan untuk tidak memberikan potongan harga atau negosiasi di luar harga resmi yang tertera pada katalog toko. Anda tetap dapat menikmati harga promo resmi pada produk berlabel Discount di halaman Shop.',
                'requires_human_handoff' => false,
                'is_active' => true,
            ],
            [
                'type' => 'boundary',
                'title' => 'Batasan Topik Di Luar Layanan PUTRIJALVA',
                'keywords' => 'politik, presiden, pemilu, judi, pinjol, kripto, resep masakan, tugas sekolah, obat medis',
                'content' => 'Mohon maaf, Aria AI Concierge memiliki batasan pengetahuan khusus untuk melayani konsultasi pakaian crop top & aksesoris, panduan ukuran (size), pengecekan harga & stok katalog, serta bantuan pesanan di butik PUTRIJALVA.',
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
                'title' => 'Panduan Retur Barang (Maksimal 1 Hari)',
                'keywords' => 'retur, barang rusak, tidak sesuai, komplain, salah kirim, cacat, pengembalian barang',
                'content' => 'PUTRIJALVA menerima retur khusus untuk barang yang rusak/cacat atau tidak sesuai pesanan. Batas waktu pelaporan retur adalah maksimal 1 hari (1x24 jam) setelah barang diterima pelanggan, disertai bukti video unboxing tanpa jeda. Penggantian unit baru 100% bebas biaya ongkir.',
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
