<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class StaticPageController extends Controller
{
    public function about(): View
    {
        return view('pages.about');
    }

    public function privacy(): View
    {
        return view('pages.privacy-policy');
    }

    public function faq(): View
    {
        $faqs = [
            [
                'category' => 'orders',
                'category_label' => 'Pemesanan & Akun',
                'question' => 'Bagaimana cara memesan busana di PUTRI.J?',
                'answer' => 'Pilih busana favorit Anda di katalog Shop, tentukan ukuran (S, M, L, XL), klik tombol "Add to Cart", lalu lanjutkan ke halaman Checkout. Jika Anda sudah login dan melengkapi alamat di menu Akun Saya, seluruh data penerima dan lokasi pengiriman akan terisi secara otomatis.',
            ],
            [
                'category' => 'orders',
                'category_label' => 'Pemesanan & Akun',
                'question' => 'Apakah saya bisa menyimpan lebih dari satu alamat pengiriman (Rumah & Kantor)?',
                'answer' => 'Tentu bisa. Masuk ke menu My Account > Alamat Pengiriman (Buku Alamat), lalu klik "+ Tambah Alamat Baru". Anda dapat menyimpan alamat Rumah, Kantor, Apartemen, maupun Kos, serta memilih alamat mana yang ingin digunakan saat berada di halaman Checkout.',
            ],
            [
                'category' => 'payment',
                'category_label' => 'Pembayaran & Konfirmasi',
                'question' => 'Metode pembayaran apa saja yang tersedia di PUTRI.J?',
                'answer' => 'Kami melayani pembayaran melalui Transfer Bank Langsung (BCA & Mandiri), QRIS Instant Payment, serta Kartu Kredit/Debit Online. Untuk kemudahan pengujian dan transaksi cepat, tersedia juga fitur Simulasi Bayar Instan maupun Konfirmasi Bukti Transfer Manual di halaman Detail Pesanan.',
            ],
            [
                'category' => 'payment',
                'category_label' => 'Pembayaran & Konfirmasi',
                'question' => 'Bagaimana cara melakukan konfirmasi setelah saya mentransfer pembayaran?',
                'answer' => 'Buka menu My Account > Pesanan Saya, klik tombol "Bayar / Detail" pada nomor pesanan Anda, lalu pilih "Konfirmasi Bukti Transfer Manual". Masukkan bank asal, nama pemilik rekening, dan nominal transfer. Tim admin kami akan memverifikasi pembayaran Anda.',
            ],
            [
                'category' => 'shipping',
                'category_label' => 'Pengiriman & Pelacakan Resi',
                'question' => 'Berapa lama pesanan saya dikemas dan dikirim?',
                'answer' => 'Setiap pesanan yang pembayarannya telah terverifikasi sebelum pukul 16.00 WIB akan dikemas rapi dalam signature packaging bersegel dan diserahkan ke kurir ekspedisi pada hari kerja yang sama. Estimasi pengiriman adalah 1-2 hari kerja untuk Jabodetabek dan 2-4 hari kerja untuk luar kota.',
            ],
            [
                'category' => 'shipping',
                'category_label' => 'Pengiriman & Pelacakan Resi',
                'question' => 'Dimana saya bisa melihat nomor resi pengiriman paket saya?',
                'answer' => 'Nomor resi kurir (JNE, SiCepat, atau J&T Express) akan otomatis muncul di halaman My Account > Pesanan Saya tepat di bawah badge status "Dikirim", serta dapat dilihat secara lengkap pada halaman Detail & Faktur Pesanan.',
            ],
            [
                'category' => 'shipping',
                'category_label' => 'Pengiriman & Pelacakan Resi',
                'question' => 'Apakah pengiriman busana diasuransikan?',
                'answer' => 'Ya, seluruh pengiriman busana PUTRI.J dilindungi oleh asuransi pengiriman penuh tanpa biaya tambahan dan dikemas dalam garment wrap pelindung debu dan air yang tersegel rapat.',
            ],
            [
                'category' => 'warranty',
                'category_label' => 'Garansi & Tukar Ukuran',
                'question' => 'Apakah saya bisa menukar ukuran baju jika kebesaran atau kekecilan?',
                'answer' => 'Ya! Kami menyediakan layanan penukaran ukuran (Complimentary Size Exchange) gratis 1 kali dalam waktu 7 hari sejak paket diterima, selama pakaian belum pernah dicuci, tidak terkena parfum/noda, dan hangtag label masih terpasang rapi.',
            ],
            [
                'category' => 'warranty',
                'category_label' => 'Garansi & Tukar Ukuran',
                'question' => 'Bagaimana prosedur klaim jika barang yang diterima cacat atau salah warna?',
                'answer' => 'Mohon sertakan video unboxing tanpa jeda dan hubungi tim kami melalui fitur Live Chat di pojok kanan bawah layar dalam waktu maksimal 2x24 jam setelah paket diterima. Kami akan mengganti dengan unit baru 100% bebas biaya ongkos kirim.',
            ],
            [
                'category' => 'care',
                'category_label' => 'Panduan Ukuran & Perawatan Bahan',
                'question' => 'Bagaimana cara menentukan ukuran baju (Size) yang paling cocok untuk saya?',
                'answer' => 'Setiap produk busana dilengkapi panduan ukuran spesifik (Lingkar Dada, Panjang Bahu, dan Panjang Baju). Anda juga dapat berkonsultasi langsung melalui Live Chat kami untuk rekomendasi ukuran berdasarkan tinggi dan berat badan.',
            ],
            [
                'category' => 'care',
                'category_label' => 'Panduan Ukuran & Perawatan Bahan',
                'question' => 'Bagaimana cara mencuci dan merawat busana berbahan Linen & Silk agar awet?',
                'answer' => 'Gunakan mode pencucian lembut (delicate cycle) atau cuci tangan dengan deterjen cair netral. Hindari penggunaan mesin pengering panas (tumble dry) dan pemutih. Jemur terbalik di tempat teduh, lalu setrika uap atau setrika suhu sedang saat pakaian masih agak lembap.',
            ],
        ];

        return view('pages.faq', compact('faqs'));
    }
}
