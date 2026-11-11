<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\BlogPost;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        $posts = [
            [
                'title' => 'Top Trends: The Effortless Capsule Wardrobe with Linen & Silk',
                'slug' => 'top-trends-the-effortless-capsule-wardrobe-with-linen-and-silk',
                'category' => 'Fashion',
                'excerpt' => 'Temukan cara memadukan kemeja French linen dan gaun midi sutra untuk tampilan elegan sehari-hari yang serbaguna dan abadi.',
                'content' => "Musim ini menghadirkan kembali pesona keanggunan busana minimalis. Pakaian berkualitas tinggi bukan sekadar tren sesaat, melainkan ekspresi gaya hidup personal yang tenang dan berkelas (quiet luxury).\n\nKunci utama membangun lemari pakaian kapsul (capsule wardrobe) adalah memilih potongan esensial yang mudah dipadupadankan—mulai dari kemeja linen oversized, celana palazzo berpinggang tinggi, hingga gaun midi sutra berpotongan bias.\n\nSetiap koleksi busana PUTRI.J dirancang dengan presisi jahitan tinggi dan material alami berserat rapat, memastikan kenyamanan maksimal dan keanggunan dari pagi hingga malam.",
                'image' => 'images/blog-1.jpg',
                'author' => 'ANNY JOHNSON',
                'published_at' => Carbon::now()->subDays(28),
            ],
            [
                'title' => 'The Art of Tailoring: Behind Our Structured Blazers',
                'slug' => 'the-art-of-tailoring-behind-our-structured-blazers',
                'category' => 'Behind The Craft',
                'excerpt' => 'Mengintip proses desain dan tailoring di balik blazer kancing ganda Valerie dengan siluet bahu tegas dan furing sutra mewah.',
                'content' => "Tailoring busana formal wanita membutuhkan keseimbangan antara ketegasan struktur dan kelenturan bergerak. Dalam merancang blazer Valerie, studio kami memilih pola double-breasted dengan jahitan lapel tajam yang memberi siluet ramping.\n\nFuring dalam berbahan sutra lembut memastikan blazer ini sejuk saat bersentuhan dengan kulit, bahkan ketika dikenakan di ruang beriklim tropis. Sangat cocok dipadukan dengan celana tailored senada maupun denim santai akhir pekan.",
                'image' => 'images/blog-2.jpg',
                'author' => 'CLARA AURELIA',
                'published_at' => Carbon::now()->subDays(24),
            ],
            [
                'title' => 'How to Care for French Washed Linen & Silk Blends',
                'slug' => 'how-to-care-for-french-washed-linen-and-silk-blends',
                'category' => 'Garment Care',
                'excerpt' => 'Panduan lengkap merawat busana berbahan linen dan satin sutra agar tekstur kain tetap halus, adem, dan awet bertahun-tahun.',
                'content' => "Bahan alami seperti linen dan sutra memiliki daya tahan luar biasa jika dirawat dengan penuh perhatian. Selalu gunakan deterjen cair netral dan cuci dengan air dingin dalam siklus lembut (delicate cycle) atau cuci tangan.\n\nHindari memeras kain terlalu kuat atau mengeringkannya di mesin pengering panas (tumble dry). Cukup kibaskan perlahan, jemur terbalik di tempat teduh, dan setrika uap saat pakaian masih agak lembap untuk hasil jatuhan kain yang sempurna.",
                'image' => 'images/blog-3.jpg',
                'author' => 'PUTRI.J Editorial',
                'published_at' => Carbon::now()->subDays(20),
            ],
            [
                'title' => 'Midi Dresses for Modern Women: From Day to Night',
                'slug' => 'midi-dresses-for-modern-women-from-day-to-night',
                'category' => 'Style Guide',
                'excerpt' => 'Tips padu padan gaun midi satin untuk bertransisi dari suasana rapat kerja siang hari menuju jamuan makan malam romantis.',
                'content' => "Gaun midi berpotongan bias seperti Aurelia Silk Dress adalah salah satu investasi busana terbaik. Di siang hari, padukan dengan blazer linen struktural dan sepatu loafer datar untuk kesan smart profesional.\n\nSaat malam tiba, lepaskan blazer dan ganti dengan heels minimalis. Drape satin sutra yang berkilau lembut di bawah pencahayaan malam akan langsung mengubah penampilan Anda menjadi memikat tanpa perlu berganti pakaian secara rumit.",
                'image' => 'images/blog-4.jpg',
                'author' => 'MARCUS VANCE',
                'published_at' => Carbon::now()->subDays(16),
            ],
            [
                'title' => 'The Irresistible Elegance of Bias Cut Silhouettes',
                'slug' => 'the-irresistible-elegance-of-bias-cut-silhouettes',
                'category' => 'Fashion',
                'excerpt' => 'Mengapa teknik potongan serong (bias cut) menjadi rahasia lekuk gaun yang mengalir indah mengikuti gerak alami tubuh.',
                'content' => "Teknik pemotongan kain pada sudut kemiringan 45 derajat (bias cut) pertama kali dipopulerkan di Paris pada era 1930-an. Berbeda dengan potongan lurus konvensional, bias cut memberikan elastisitas alami pada serat kain tanpa perlu mencampurkan spandex berlebih.\n\nHasilnya adalah rok dan gaun yang memeluk lekuk tubuh dengan lembut, tidak terasa ketat, dan mengayun anggun di setiap langkah kaki Anda.",
                'image' => 'images/blog-1.jpg',
                'author' => 'PUTRI.J Atelier',
                'published_at' => Carbon::now()->subDays(12),
            ],
            [
                'title' => 'Mindful Fashion: Sustainable Fabrics & Ethical Craft',
                'slug' => 'mindful-fashion-sustainable-fabrics-and-ethical-craft',
                'category' => 'Sustainability',
                'excerpt' => 'Komitmen PUTRI.J terhadap pemilihan serat alami terbarukan, pengurangan jejak karbon, dan penghargaan bagi para penjahit lokal.',
                'content' => "Keanggunan sejati selaras dengan kepedulian terhadap lingkungan dan sesama. Seluruh busana PUTRI.J diproduksi dalam jumlah terbatas (limited batch) untuk menghindari kelebihan limbah tekstil.\n\nKami bekerja sama langsung dengan para perajin jahit berbakat di Indonesia, memberikan ruang kerja yang adil, dan memastikan setiap helai pakaian dibuat untuk dipakai dan dicintai dalam jangka waktu lama.",
                'image' => 'images/blog-2.jpg',
                'author' => 'HELENA P.',
                'published_at' => Carbon::now()->subDays(8),
            ],
        ];

        foreach ($posts as $post) {
            BlogPost::updateOrCreate(['slug' => $post['slug']], $post);
        }
    }
}
