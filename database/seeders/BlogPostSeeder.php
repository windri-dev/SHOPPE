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
                'title' => 'Tren Teratas: Membangun Capsule Wardrobe Anggun dengan Linen & Sutra',
                'title_en' => 'Top Trends: The Effortless Capsule Wardrobe with Linen & Silk',
                'slug' => 'top-trends-the-effortless-capsule-wardrobe-with-linen-and-silk',
                'category' => 'Fashion',
                'excerpt' => 'Temukan cara memadukan kemeja French linen dan gaun midi sutra untuk tampilan elegan sehari-hari yang serbaguna dan abadi.',
                'excerpt_en' => 'Discover how to style relaxed French linen shirts with fluid silk midi dresses for a versatile, timeless everyday luxury look.',
                'content' => "Musim ini menghadirkan kembali pesona keanggunan busana minimalis. Pakaian berkualitas tinggi bukan sekadar tren sesaat, melainkan ekspresi gaya hidup personal yang tenang dan berkelas (quiet luxury).\n\nKunci utama membangun lemari pakaian kapsul (capsule wardrobe) adalah memilih potongan esensial yang mudah dipadupadankan—mulai dari kemeja linen oversized, celana palazzo berpinggang tinggi, hingga gaun midi sutra berpotongan bias.\n\nSetiap koleksi busana PUTRI.J dirancang dengan presisi jahitan tinggi dan material alami berserat rapat, memastikan kenyamanan maksimal dan keanggunan dari pagi hingga malam.",
                'content_en' => "This season welcomes the enduring appeal of minimalist fashion. Quality garments transcend temporary trends to become a personal expression of quiet luxury.\n\nThe foundation of a modern capsule wardrobe lies in essential, versatile pieces: oversized washed linen shirts, high-waisted palazzo trousers, and bias-cut silk midi dresses.\n\nEach PUTRI.J piece is crafted with meticulous tailoring and natural breathable fibers, ensuring effortless refinement from morning to evening.",
                'image' => 'images/blog-1.jpg',
                'author' => 'ANNY JOHNSON',
                'published_at' => Carbon::now()->subDays(28),
            ],
            [
                'title' => 'Seni Tailoring: Di Balik Mahakarya Blazer Berstruktur Kami',
                'title_en' => 'The Art of Tailoring: Behind Our Structured Blazers',
                'slug' => 'the-art-of-tailoring-behind-our-structured-blazers',
                'category' => 'Behind The Craft',
                'excerpt' => 'Mengintip proses desain dan tailoring di balik blazer kancing ganda Valerie dengan siluet bahu tegas dan furing sutra mewah.',
                'excerpt_en' => 'A glimpse into the atelier craftsmanship behind our double-breasted Valerie blazer, featuring sharp lapels and luxurious silk lining.',
                'content' => "Tailoring busana formal wanita membutuhkan keseimbangan antara ketegasan struktur dan kelenturan bergerak. Dalam merancang blazer Valerie, studio kami memilih pola double-breasted dengan jahitan lapel tajam yang memberi siluet ramping.\n\nFuring dalam berbahan sutra lembut memastikan blazer ini sejuk saat bersentuhan dengan kulit, bahkan ketika dikenakan di ruang beriklim tropis. Sangat cocok dipadukan dengan celana tailored senada maupun denim santai akhir pekan.",
                'content_en' => "Tailoring womenswear demands a delicate balance between defined structure and fluid movement. In designing the Valerie blazer, our atelier selected a sharp double-breasted cut with precision peak lapels for a flattering silhouette.\n\nThe pure silk lining ensures a remarkably cool, smooth feel against the skin, ideal for tropical and temperate climates alike. Pair it with matching tailored trousers or relaxed denim.",
                'image' => 'images/blog-2.jpg',
                'author' => 'CLARA AURELIA',
                'published_at' => Carbon::now()->subDays(24),
            ],
            [
                'title' => 'Panduan Merawat Busana French Washed Linen & Paduan Sutra',
                'title_en' => 'How to Care for French Washed Linen & Silk Blends',
                'slug' => 'how-to-care-for-french-washed-linen-and-silk-blends',
                'category' => 'Garment Care',
                'excerpt' => 'Panduan lengkap merawat busana berbahan linen dan satin sutra agar tekstur kain tetap halus, adem, dan awet bertahun-tahun.',
                'excerpt_en' => 'A definitive guide to caring for delicate washed linen and mulberry silk fabrics so their texture stays soft, breathable, and enduring for years.',
                'content' => "Bahan alami seperti linen dan sutra memiliki daya tahan luar biasa jika dirawat dengan penuh perhatian. Selalu gunakan deterjen cair netral dan cuci dengan air dingin dalam siklus lembut (delicate cycle) atau cuci tangan.\n\nHindari memeras kain terlalu kuat atau mengeringkannya di mesin pengering panas (tumble dry). Cukup kibaskan perlahan, jemur terbalik di tempat teduh, dan setrika uap saat pakaian masih agak lembap untuk hasil jatuhan kain yang sempurna.",
                'content_en' => "Natural fabrics like linen and silk possess remarkable longevity when cared for with mindful attention. Always opt for a pH-neutral liquid detergent and wash with cold water on a delicate or gentle cycle, or wash by hand.\n\nAvoid wringing tightly or using high-heat tumble dryers. Gently shake out excess water, line dry inside out in shaded air, and use a light steam iron while the garment is still slightly damp for an impeccable natural drape.",
                'image' => 'images/blog-3.jpg',
                'author' => 'PUTRI.J Editorial',
                'published_at' => Carbon::now()->subDays(20),
            ],
            [
                'title' => 'Gaun Midi untuk Wanita Modern: Dari Jam Kerja Siang hingga Makan Malam',
                'title_en' => 'Midi Dresses for Modern Women: From Day to Night',
                'slug' => 'midi-dresses-for-modern-women-from-day-to-night',
                'category' => 'Style Guide',
                'excerpt' => 'Tips padu padan gaun midi satin untuk bertransisi dari suasana rapat kerja siang hari menuju jamuan makan malam romantis.',
                'excerpt_en' => 'Styling secrets to transition your silk midi dress effortlessly from corporate boardroom meetings to romantic candlelit dinners.',
                'content' => "Gaun midi berpotongan bias seperti Aurelia Silk Dress adalah salah satu investasi busana terbaik. Di siang hari, padukan dengan blazer linen struktural dan sepatu loafer datar untuk kesan smart profesional.\n\nSaat malam tiba, lepaskan blazer dan ganti dengan heels minimalis. Drape satin sutra yang berkilau lembut di bawah pencahayaan malam akan langsung mengubah penampilan Anda menjadi memikat tanpa perlu berganti pakaian secara rumit.",
                'content_en' => "A bias-cut midi dress like our Aurelia Silk Dress is one of the most rewarding wardrobe investments. By day, layer it beneath a structured linen blazer with sleek flat loafers for polished professionalism.\n\nAs evening falls, shed the blazer and slip into minimalist kitten heels. The lustrous satin drape catches the evening light with subtle radiance, effortlessly elevating your look without needing a change of clothes.",
                'image' => 'images/blog-4.jpg',
                'author' => 'MARCUS VANCE',
                'published_at' => Carbon::now()->subDays(16),
            ],
            [
                'title' => 'Pesona Keanggunan Siluet Gaun Potongan Bias (Bias Cut)',
                'title_en' => 'The Irresistible Elegance of Bias Cut Silhouettes',
                'slug' => 'the-irresistible-elegance-of-bias-cut-silhouettes',
                'category' => 'Fashion',
                'excerpt' => 'Mengapa teknik potongan serong (bias cut) menjadi rahasia lekuk gaun yang mengalir indah mengikuti gerak alami tubuh.',
                'excerpt_en' => 'Why the 45-degree bias cut technique remains the secret behind fluid dresses that celebrate natural movement without restriction.',
                'content' => "Teknik pemotongan kain pada sudut kemiringan 45 derajat (bias cut) pertama kali dipopulerkan di Paris pada era 1930-an. Berbeda dengan potongan lurus konvensional, bias cut memberikan elastisitas alami pada serat kain tanpa perlu mencampurkan spandex berlebih.\n\nHasilnya adalah rok dan gaun yang memeluk lekuk tubuh dengan lembut, tidak terasa ketat, dan mengayun anggun di setiap langkah kaki Anda.",
                'content_en' => "Pioneered in 1930s Paris, the technique of cutting fabric diagonally at a 45-degree angle (bias cut) imparts natural elasticity to woven textiles without synthetic elastane.\n\nThe result is skirts and dresses that skim the body with gentle fluidity, creating an undulating silhouette that sways gracefully with every step you take.",
                'image' => 'images/blog-1.jpg',
                'author' => 'PUTRI.J Atelier',
                'published_at' => Carbon::now()->subDays(12),
            ],
            [
                'title' => 'Mode Berkesadaran: Kain Ramah Lingkungan & Ketelitian Perajin',
                'title_en' => 'Mindful Fashion: Sustainable Fabrics & Ethical Craft',
                'slug' => 'mindful-fashion-sustainable-fabrics-and-ethical-craft',
                'category' => 'Sustainability',
                'excerpt' => 'Komitmen PUTRI.J terhadap pemilihan serat alami terbarukan, pengurangan jejak karbon, dan penghargaan bagi para penjahit lokal.',
                'excerpt_en' => "PUTRI.J's commitment to renewable natural fibers, low carbon footprint, and fair, dignified craftsmanship for Indonesian artisans.",
                'content' => "Keanggunan sejati selaras dengan kepedulian terhadap lingkungan dan sesama. Seluruh busana PUTRI.J diproduksi dalam jumlah terbatas (limited batch) untuk menghindari kelebihan limbah tekstil.\n\nKami bekerja sama langsung dengan para perajin jahit berbakat di Indonesia, memberikan ruang kerja yang adil, dan memastikan setiap helai pakaian dibuat untuk dipakai dan dicintai dalam jangka waktu lama.",
                'content_en' => "True elegance exists in harmony with the environment and the craftspeople who bring garments to life. All PUTRI.J collections are produced in small, limited batches to prevent textile excess.\n\nWe collaborate directly with skilled tailors and textile artisans across Indonesia, providing fair wages and a dignified creative environment, ensuring every piece is cherished for years to come.",
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
