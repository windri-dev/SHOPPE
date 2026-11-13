<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductReviewSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::all()->keyBy('slug');
        $clara = User::where('email', 'buyer@shoppe.com')->first();

        $reviews = [
            // Aurelia Silk Bias Cut Midi Dress (Produk Utama)
            [
                'product_slug' => 'aurelia-silk-bias-cut-midi-dress',
                'user_id' => null,
                'customer_name' => 'zainulll12',
                'customer_email' => 'zainul@example.com',
                'rating' => 5,
                'size_purchased' => 'Size M, Champagne Silk',
                'headline' => null,
                'quality_tag' => 'Sangat bagus & adem',
                'fit_tag' => 'Pas di badan',
                'comment' => 'barang mendarat dengan aman, seller responsif dan langsung respon jika ada pertanyaan. semua sesuai pesanan dan ok punya, thank you min!',
                'images' => [
                    'images/product-1.png',
                    'images/product-2.png',
                    'images/product-3.png',
                ],
                'seller_reply' => 'Hai Kak, Terimakasih sudah berbelanja di toko Kami, kami harap Kakak senang dengan Produk yg diterima dan juga Kakak bisa terus menjadi pelanggan setia kami, pastikan kakak sudah Follow toko kami untuk mengetahui promo menarik lainnya. Kami tunggu pemesanan selanjutnya. Terimakasih :)',
                'seller_replied_at' => now()->subDays(2),
                'is_verified_purchase' => true,
                'is_approved' => true,
                'helpful_count' => 11,
                'created_at' => now()->subDays(3)->setTime(20, 55),
            ],
            [
                'product_slug' => 'aurelia-silk-bias-cut-midi-dress',
                'user_id' => null,
                'customer_name' => 'h*****a',
                'customer_email' => 'handayana@example.com',
                'rating' => 5,
                'size_purchased' => 'Size S, Midnight Black',
                'headline' => null,
                'quality_tag' => 'Sutra premium jatuh rapi',
                'fit_tag' => 'Sesuai size chart',
                'comment' => 'Pengemasannya super duper aman. 2 lapis bubble wrap + boks eksklusif PUTRIJALVA (selain pouch kain).. barangnya juga ori. Terima kasih',
                'images' => [
                    'images/product-2.png',
                    'images/product-1.png',
                ],
                'seller_reply' => 'Hai Kak, Terimakasih sudah berbelanja di toko Kami, kami harap Kakak senang dengan Produk yg diterima dan juga Kakak bisa terus menjadi pelanggan setia kami, pastikan kakak sudah Follow toko kami untuk mengetahui promo menarik lainnya. Kami tunggu pemesanan selanjutnya. Terimakasih :)',
                'seller_replied_at' => now()->subDays(5),
                'is_verified_purchase' => true,
                'is_approved' => true,
                'helpful_count' => 7,
                'created_at' => now()->subDays(6)->setTime(11, 50),
            ],
            [
                'product_slug' => 'aurelia-silk-bias-cut-midi-dress',
                'user_id' => $clara?->id,
                'customer_name' => 'clara.aurelia',
                'customer_email' => 'buyer@shoppe.com',
                'rating' => 5,
                'size_purchased' => 'Size M, Champagne Silk',
                'headline' => null,
                'quality_tag' => 'Mewah, drape kain sangat halus',
                'fit_tag' => 'Fit anggun & flowy',
                'comment' => 'Potongan bias cut-nya bikin siluet tubuh kelihatan ramping. Saya pakai untuk gala dinner dan dapat banyak pujian. Tali spageti adjustable jadi gampang diatur. Sangat recommended!',
                'images' => [
                    'images/product-1.png',
                ],
                'seller_reply' => 'Terima kasih banyak Kak Clara atas review bintang 5 dan apresiasi luar biasa untuk koleksi gaun sutra PUTRIJALVA. Senang sekali gaunnya membuat penampilan Kakak memukau di acara gala dinner! Ditunggu pesanan berikutnya ya Kak.',
                'seller_replied_at' => now()->subDays(9),
                'is_verified_purchase' => true,
                'is_approved' => true,
                'helpful_count' => 19,
                'created_at' => now()->subDays(10)->setTime(14, 20),
            ],
            [
                'product_slug' => 'aurelia-silk-bias-cut-midi-dress',
                'user_id' => null,
                'customer_name' => 'm*****9',
                'customer_email' => 'maya9@example.com',
                'rating' => 4,
                'size_purchased' => 'Size L, Rose Pearl',
                'headline' => null,
                'quality_tag' => 'Adem dan jahitan kelim rapi',
                'fit_tag' => 'Cukup pas',
                'comment' => 'Warna aslinya manis banget. Bahannya dingin dan tidak menerawang walau silk. Pengiriman cepat sampai hanya 2 hari ke Surabaya.',
                'images' => [],
                'seller_reply' => 'Terima kasih atas ulasannya Kak! Senang sekali produk tiba dengan aman dan cepat. Semoga selalu menemani hari-hari istimewa Kakak :)',
                'seller_replied_at' => now()->subDays(12),
                'is_verified_purchase' => true,
                'is_approved' => true,
                'helpful_count' => 4,
                'created_at' => now()->subDays(13)->setTime(16, 45),
            ],

            // Serena Linen Wrap Tiered Maxi Dress
            [
                'product_slug' => 'serena-linen-wrap-tiered-maxi-dress',
                'user_id' => null,
                'customer_name' => 'dian_kusuma',
                'customer_email' => 'dian@example.com',
                'rating' => 5,
                'size_purchased' => 'Size L, Oatmeal Linen',
                'headline' => null,
                'quality_tag' => 'French washed linen adem',
                'fit_tag' => 'Longgar & nyaman',
                'comment' => 'Linennya lemas dan empuk tidak kaku sama sekali. Model tiered maxi-nya memberi volume yang cantik saat dipakai jalan-jalan santai.',
                'images' => [
                    'images/product-2.png',
                ],
                'seller_reply' => 'Terima kasih banyak Kak Dian atas ulasan positifnya! French washed linen memang pilihan terbaik untuk cuaca tropis kita :)',
                'seller_replied_at' => now()->subDays(14),
                'is_verified_purchase' => true,
                'is_approved' => true,
                'helpful_count' => 11,
                'created_at' => now()->subDays(15)->setTime(9, 15),
            ],
            [
                'product_slug' => 'serena-linen-wrap-tiered-maxi-dress',
                'user_id' => null,
                'customer_name' => 'r*****a',
                'customer_email' => 'ratih@example.com',
                'rating' => 5,
                'size_purchased' => 'Size M, Olive Green',
                'headline' => null,
                'quality_tag' => 'Kain organik sejuk',
                'fit_tag' => 'Pas di pinggang',
                'comment' => 'Wrap dress-nya fleksibel bisa diatur tali pinggangnya. Worth every penny!',
                'images' => [],
                'seller_reply' => null,
                'seller_replied_at' => null,
                'is_verified_purchase' => true,
                'is_approved' => true,
                'helpful_count' => 5,
                'created_at' => now()->subDays(8)->setTime(18, 30),
            ],

            // Celeste Pleated Halter Evening Gown
            [
                'product_slug' => 'celeste-pleated-halter-evening-gown',
                'user_id' => null,
                'customer_name' => 'salsabila_n',
                'customer_email' => 'salsa@example.com',
                'rating' => 5,
                'size_purchased' => 'Size S, Obsidian Black',
                'headline' => null,
                'quality_tag' => 'Plisket rapat & tidak mudah pudar',
                'fit_tag' => 'Semampai',
                'comment' => 'Showstopper abis! Dipakai di pesta pernikahan dan banyak yang nanya beli di mana. Bagian halternecknya nyaman.',
                'images' => [
                    'images/product-3.png',
                ],
                'seller_reply' => 'Terima kasih banyak Kak! Senang sekali gaun halterneck PUTRIJALVA sukses mencuri perhatian di acara pesta!',
                'seller_replied_at' => now()->subDays(18),
                'is_verified_purchase' => true,
                'is_approved' => true,
                'helpful_count' => 16,
                'created_at' => now()->subDays(19)->setTime(21, 10),
            ],

            // Noemi Minimalist Poplin Shirt Dress
            [
                'product_slug' => 'noemi-minimalist-poplin-shirt-dress',
                'user_id' => null,
                'customer_name' => 'anindita_t',
                'customer_email' => 'anindita@example.com',
                'rating' => 5,
                'size_purchased' => 'Size M, Crisp White',
                'headline' => null,
                'quality_tag' => 'Poplin katun tebal tidak terawang',
                'fit_tag' => 'Oversized chic',
                'comment' => 'Kemeja dress paling chic untuk ke kantor & cafe santai. Kerah kemejanya tegas memberi kesan profesional.',
                'images' => [],
                'seller_reply' => 'Terima kasih banyak Kak Anindita! Selamat beraktivitas dengan koleksi kemeja chic kami :)',
                'seller_replied_at' => now()->subDays(9),
                'is_verified_purchase' => true,
                'is_approved' => true,
                'helpful_count' => 7,
                'created_at' => now()->subDays(10)->setTime(13, 05),
            ],

            // Vivienne Structured Crepe Blazer
            [
                'product_slug' => 'vivienne-structured-crepe-blazer',
                'user_id' => null,
                'customer_name' => 'steph_w',
                'customer_email' => 'steph@example.com',
                'rating' => 5,
                'size_purchased' => 'Size M, Tailored Camel',
                'headline' => null,
                'quality_tag' => 'Bahu terstruktur & furing halus',
                'fit_tag' => 'Power woman fit',
                'comment' => 'Padding bahunya pas tidak ketebalan, puring dalamnya halus satin premium. Dipakai meeting kerja langsung elevate penampilan.',
                'images' => [
                    'images/product-1.png',
                ],
                'seller_reply' => 'Terima kasih ulasan bintang 5-nya Kak Steph! Power blazer ini memang andalan untuk boss lady look yang percaya diri.',
                'seller_replied_at' => now()->subDays(21),
                'is_verified_purchase' => true,
                'is_approved' => true,
                'helpful_count' => 19,
                'created_at' => now()->subDays(22)->setTime(17, 40),
            ],
        ];

        foreach ($reviews as $item) {
            $product = $products->get($item['product_slug']);
            if (! $product) {
                continue;
            }

            ProductReview::create([
                'product_id' => $product->id,
                'user_id' => $item['user_id'],
                'customer_name' => $item['customer_name'],
                'customer_email' => $item['customer_email'],
                'rating' => $item['rating'],
                'size_purchased' => $item['size_purchased'],
                'headline' => $item['headline'],
                'quality_tag' => $item['quality_tag'],
                'fit_tag' => $item['fit_tag'],
                'comment' => $item['comment'],
                'images' => $item['images'],
                'seller_reply' => $item['seller_reply'],
                'seller_replied_at' => $item['seller_replied_at'],
                'is_verified_purchase' => $item['is_verified_purchase'],
                'is_approved' => $item['is_approved'],
                'helpful_count' => $item['helpful_count'],
                'created_at' => $item['created_at'],
                'updated_at' => $item['created_at'],
            ]);
        }
    }
}
