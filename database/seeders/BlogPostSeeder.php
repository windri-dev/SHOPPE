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
                'title' => 'Top Trends From Spring: Minimalist Gold Layering',
                'slug' => 'top-trends-from-spring-minimalist-gold-layering',
                'category' => 'Fashion',
                'excerpt' => 'Temukan cara memadukan kalung rantai halus dan liontin koin berlapis emas 18K untuk tampilan elegan sehari-hari tanpa terlihat berlebihan.',
                'content' => "Musim semi tahun ini menghadirkan kembali pesona keanggunan minimalis. Perhiasan bukan lagi sekadar pelengkap busana, melainkan ekspresi karakter personal yang tenang namun berkelas.\n\nKunci utama dari teknik layering kalung adalah memadukan panjang rantai yang berbeda—mulai dari choker 38cm, rantai liontin medium 42cm, hingga siluet panjang 50cm. Kombinasi tekstur antara rantai tipis dan liontin koin Plaine Necklace menciptakan dimensi visual yang hangat di kulit Anda.\n\nSetiap perhiasan PUTRI.J dibuat dari kuningan pilihan dengan pelapisan emas 18 karat yang ramah untuk kulit sensitif (hypoallergenic), memastikan kilau yang tahan lama dari pagi hingga acara malam hari.",
                'image' => 'images/blog-1.jpg',
                'author' => 'ANNY JOHNSON',
                'published_at' => Carbon::now()->subDays(28),
            ],
            [
                'title' => 'The Art of Botanical Hair Accessories',
                'slug' => 'the-art-of-botanical-hair-accessories',
                'category' => 'Style',
                'excerpt' => 'Dari jepit rambut Kaede hingga seri Yuki Blossom, pelajari inspirasi desain organik di balik koleksi aksesori rambut artisan kami.',
                'content' => "Aksesori rambut telah berevolusi menjadi pusat perhatian dalam tata rias modern. Koleksi Kaede dan Yuki Hair Pin kami terinspirasi dari siluet dedaunan musim gugur dan kelopak bunga musim semi di Kyoto.\n\nDibentuk secara presisi dengan sentuhan akhir satin-matte dan kilau champagne gold, set tiga jepit rambut ini dirancang ringan namun memiliki daya cengkeram kuat untuk tatanan rambut sanggul rendah (low bun) maupun gelombang natural.\n\nPadukan Kaede Hair Pin dengan anting lingkaran kecil seperti Ollie Hoop Earrings untuk menjaga keseimbangan proporsi di sekitar wajah Anda.",
                'image' => 'images/blog-2.jpg',
                'author' => 'CLARA AURELIA',
                'published_at' => Carbon::now()->subDays(24),
            ],
            [
                'title' => 'How to Care for Your 18k Gold-Dipped Jewelry',
                'slug' => 'how-to-care-for-your-18k-gold-dipped-jewelry',
                'category' => 'Jewelry Care',
                'excerpt' => 'Panduan lengkap merawat koleksi anting, kalung, dan cincin lapis emas agar tetap berkilau seperti baru selama bertahun-tahun.',
                'content' => "Perhiasan berlapis emas 18k membutuhkan sentuhan perawatan sederhana agar kilaunya tetap terjaga. Langkah pertama yang paling penting adalah mengenakan perhiasan Anda paling akhir—setelah parfum, lotion, dan hairspray mengering sempurna di kulit.\n\nSetelah selesai beraktivitas, usap permukaan perhiasan secara lembut menggunakan kain mikrofiber kering untuk mengangkat minyak alami kulit. Simpan setiap pasang anting dan kalung secara terpisah di dalam kantung beludru (velvet pouch) bawaan PUTRI.J guna mencegah goresan antar logam.\n\nDengan perawatan rutin yang tepat, koleksi Lira Earrings dan Hal Earrings Anda akan senantiasa memancarkan rona emas hangat setiap kali dikenakan.",
                'image' => 'images/blog-3.jpg',
                'author' => 'PUTRI.J Editorial',
                'published_at' => Carbon::now()->subDays(20),
            ],
            [
                'title' => 'Sculptural Hoops: Behind the Lira & Hal Collection',
                'slug' => 'sculptural-hoops-behind-the-lira-and-hal-collection',
                'category' => 'Behind The Craft',
                'excerpt' => 'Mengintip proses kreatif studio kami dalam merancang anting berstruktur arsitektural yang ringan dan nyaman dipakai sepanjang hari.',
                'content' => "Saat merancang seri Lira dan Hal Earrings, tim artisan kami berfokus pada satu pertanyaan utama: bagaimana menciptakan anting berdimensi tegas namun tetap terasa seenteng bulu saat dikenakan selama 12 jam?\n\nJawabannya terletak pada teknik pengecoran berongga presisi dan distribusi titik berat yang seimbang pada pengait telinga bebas nikel. Lengkungan ganda pada Hal Earrings memantulkan cahaya dari berbagai sudut, memberikan efek kilau dinamis di setiap gerakan kepala.\n\nBaik dipadukan dengan kemeja linen kasual maupun gaun malam sutra, siluet skulptural ini selalu berhasil mencuri perhatian dengan cara yang paling bersahaja.",
                'image' => 'images/blog-4.jpg',
                'author' => 'MARCUS VANCE',
                'published_at' => Carbon::now()->subDays(16),
            ],
            [
                'title' => 'The Irresistible Allure of Baroque Pearls',
                'slug' => 'the-irresistible-allure-of-baroque-pearls',
                'category' => 'Fashion',
                'excerpt' => 'Mengapa ketidaksempurnaan bentuk mutiara air tawar alami menjadi simbol kemewahan baru generasi kini.',
                'content' => "Era mutiara bulat sempurna yang kaku telah berganti dengan pesona organik mutiara baroque. Bentuknya yang asimetris, permukaan bergelombang, dan pendaran warna pelangi (orient) menjadikan setiap butir mutiara sebagai karya seni murni dari alam.\n\nKoleksi Solace Baroque Pearl Drop kami merayakan keunikan setiap mutiara tanpa memaksakan keseragaman cetakan pabrik. Dipadukan dengan kuningan berlapis emas vermeil, mutiara ini menciptakan harmoni antara keanggunan klasik dan gaya kontemporer.\n\nSangat serasi dipadukan dengan blazer santai warna krem atau gaun slip sutra untuk brunch akhir pekan maupun jamuan resmi.",
                'image' => 'images/blog-1.jpg',
                'author' => 'ANNY JOHNSON',
                'published_at' => Carbon::now()->subDays(12),
            ],
            [
                'title' => 'Ring Stacking 101: Rules to Break and Follow',
                'slug' => 'ring-stacking-101-rules-to-break-and-follow',
                'category' => 'Style',
                'excerpt' => 'Cara mengombinasikan cincin dome lebar, midi band bertekstur, dan barisan pavé untuk kurasi jemari yang estetik.',
                'content' => "Seni menumpuk cincin (ring stacking) adalah cara paling menyenangkan untuk mengekspresikan karakter personal Anda. Kunci dari stack yang seimbang adalah bermain dengan kontras volume dan tekstur.\n\nMulailah dengan satu 'anchor ring' yang bervolume tebal—seperti Vermeil Sculpted Dome Ring di jari telunjuk atau jari tengah. Kemudian imbangi dengan cincin tipis Elysian Pavé Band dan sentuhan midi ring di atas buku jari.\n\nJangan takut memadukan tekstur matte dan glossy, atau bahkan menyelipkan sentuhan batu permata untuk memberi aksen warna fokal.",
                'image' => 'images/blog-2.jpg',
                'author' => 'CLARA AURELIA',
                'published_at' => Carbon::now()->subDays(9),
            ],
            [
                'title' => 'Hypoallergenic Jewelry: What Sensitive Skin Needs',
                'slug' => 'hypoallergenic-jewelry-what-sensitive-skin-needs',
                'category' => 'Jewelry Care',
                'excerpt' => 'Kenali penyebab iritasi kulit akibat perhiasan imitasi dan mengapa standar pelapisan nikel-free PUTRI.J aman untuk Anda.',
                'content' => "Gatal, kemerahan, atau kulit menghitam setelah memakai perhiasan murah umumnya disebabkan oleh pelepasan ion nikel dan timbal saat bersentuhan dengan keringat.\n\nDi PUTRI.J, kami menerapkan standar ketat Uni Eropa (REACH compliance) untuk memastikan semua logam dasar sepenuhnya bebas nikel dan bebas timbal. Lapisan emas 18k yang kami gunakan memiliki ketebalan di atas rata-rata industri dengan pelindung e-coating transparan.\n\nHasilnya adalah perhiasan yang terasa sejuk, tidak memicu alergi, dan aman dipakai berjam-jam bahkan bagi Anda yang memiliki riwayat eksim atau dermatitis kontak.",
                'image' => 'images/blog-3.jpg',
                'author' => 'PUTRI.J Editorial',
                'published_at' => Carbon::now()->subDays(6),
            ],
            [
                'title' => 'The Ultimate Jewelry Gift Guide for Every Milestone',
                'slug' => 'the-ultimate-jewelry-gift-guide-for-every-milestone',
                'category' => 'Gift Guide',
                'excerpt' => 'Rekomendasi hadiah bermakna mulai dari kelulusan, ulang tahun pernikahan, hingga apresiasi untuk diri sendiri (self-reward).',
                'content' => "Memberikan perhiasan adalah simbol keabadian memori. Saat memilih hadiah, pertimbangkan gaya hidup dan selera busana penerima:\n\n- Untuk Sahabat atau Bridesmaid: Set jepit rambut Kaede atau gelang Clara Tennis Chain adalah pilihan manis yang selalu disukai.\n- Untuk Ibu atau Pasangan: Signature Solstice Layering Set dalam kotak beludru emerald menghadirkan kesan mewah yang personal.\n- Untuk Diri Sendiri: Plaine Medallion Necklace atau jam tangan Chrono Minimalist menjadi pengingat atas pencapaian hidup Anda.\n\nSetiap pesanan di PUTRI.J dapat dilengkapi dengan kartu ucapan bertuliskan tangan dan pita satin eksklusif.",
                'image' => 'images/blog-4.jpg',
                'author' => 'MARCUS VANCE',
                'published_at' => Carbon::now()->subDays(3),
            ],
            [
                'title' => 'Sustainably Crafted: Our Commitment to Recycled Metals',
                'slug' => 'sustainably-crafted-our-commitment-to-recycled-metals',
                'category' => 'Behind The Craft',
                'excerpt' => 'Langkah nyata kami dalam mengurangi jejak karbon melalui pemanfaatan 100% kuningan dan perak daur ulang bersertifikasi.',
                'content' => "Kemewahan sejati tidak boleh mengorbankan kelestarian bumi kita. Oleh karena itu, seluruh lini perhiasan PUTRI.J dibuat menggunakan kuningan dan perak daur ulang bermutu tinggi yang diperoleh dari rantai pasok etis bersertifikat RJC (Responsible Jewellery Council).\n\nProses pemurnian modern memungkinkan logam daur ulang memiliki kemurnian dan daya tahan yang setara persis dengan logam tambang baru, namun dengan jejak emisi karbon hingga 70% lebih rendah.\n\nKami percaya bahwa perhiasan indah yang Anda kenakan hari ini harus menjadi warisan yang membanggakan bagi masa depan generasi mendatang.",
                'image' => 'images/blog-1.jpg',
                'author' => 'ANNY JOHNSON',
                'published_at' => Carbon::now()->subDays(1),
            ],
        ];

        foreach ($posts as $post) {
            BlogPost::updateOrCreate(['slug' => $post['slug']], $post);
        }
    }
}
