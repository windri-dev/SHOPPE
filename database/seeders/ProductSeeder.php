<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all()->keyBy('slug');

        $products = [
            // ================= DRESSES =================
            [
                'sku' => 'SKU-DRS-001',
                'name' => 'Aurelia Silk Bias Cut Midi Dress',
                'slug' => 'aurelia-silk-bias-cut-midi-dress',
                'category_id' => $categories['dresses']->id,
                'price' => 65.00,
                'original_price' => 85.00,
                'stock' => 18,
                'is_featured' => true,
                'is_active' => true,
                'badge' => 'Best Seller',
                'sales_count' => 142,
                'description' => 'Gaun midi anggun berpotongan bias drape lembut dari material silk-blend satin premium. Jatuh mengikuti lekuk tubuh dengan tali spageti yang dapat diatur. Sangat cocok untuk acara semi-formal, dinner romantis, maupun garden party elegan.

Fitur Utama:
• Material Silk-Blend Premium: Karakter kain halus, lembut di kulit, dan berkesan jatuh rapi tanpa menerawang.
• Siluet Bias Cut: Membalut lekuk tubuh secara proporsional dan memberikan efek siluet jenjang feminin.
• Tali Bahu Adjustable: Mudah disesuaikan panjang pendeknya sesuai kenyamanan Anda.
• Desain Versatile: Anggun dipadukan bersama blazer formal, cardigan rajut, atau dikenakan langsung untuk pesta malam.

Spesifikasi Produk:
• Bahan: Silk-Blend Satin Grade A (Adem, Ringan & Berkilau Mewah)
• Panjang Gaun: Midi (± 118 - 122 cm)
• Pilihan Ukuran: S (LD 88 cm), M (LD 94 cm), L (LD 100 cm), XL (LD 106 cm)
• Petunjuk Perawatan: Cuci lembut dengan tangan (cold wash), jemur terbalik di tempat teduh, setrika suhu sedang.

Gaun ini dilengkapi furing lembut di bagian dada untuk kenyamanan ekstra dan tidak jiplak. Pilihan sempurna untuk menciptakan penampilan elegan yang timeless dan memikat di setiap momen spesial Anda!

<div class="desc-banner-img">
  <img src="/images/product-1.png" alt="Keanggunan Siluet & Material Silk-Blend Premium">
</div>

<div class="desc-feature-grid">
  <div class="desc-feature-card">
    <div class="feat-title">✨ Karakter Mewah</div>
    <div class="feat-desc">Kilau satin natural elegan</div>
  </div>
  <div class="desc-feature-card">
    <div class="feat-title">🌿 Nyaman Dipakai</div>
    <div class="feat-desc">Serat bernapas tidak gerah</div>
  </div>
  <div class="desc-feature-card">
    <div class="feat-title">👗 Jahitan Rapi</div>
    <div class="feat-desc">Standar butik profesional</div>
  </div>
  <div class="desc-feature-card">
    <div class="feat-title">⚡ Siap Kirim</div>
    <div class="feat-desc">Packing aman & higienis</div>
  </div>
</div>',
                'images' => ['images/product-1.png', 'images/product-2.png'],
                'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4',
            ],
            [
                'sku' => 'SKU-DRS-002',
                'name' => 'Serena Linen Wrap Tiered Maxi Dress',
                'slug' => 'serena-linen-wrap-tiered-maxi-dress',
                'category_id' => $categories['dresses']->id,
                'price' => 58.00,
                'original_price' => 72.00,
                'stock' => 12,
                'is_featured' => true,
                'is_active' => true,
                'badge' => '-20%',
                'sales_count' => 98,
                'description' => 'Maxi wrap dress berbahan 100% linen alami Indonesia yang sejuk dan breathable. Dilengkapi aksen belt pinggang terikat dan rok berjenjang (tiered flare) yang mengayun indah saat melangkah.',
                'images' => ['images/product-2.png', 'images/product-3.png'],
                'video_url' => null,
            ],
            [
                'sku' => 'SKU-DRS-003',
                'name' => 'Celeste Pleated Halter Evening Gown',
                'slug' => 'celeste-pleated-halter-evening-gown',
                'category_id' => $categories['dresses']->id,
                'price' => 89.00,
                'original_price' => null,
                'stock' => 2, // Low stock
                'is_featured' => true,
                'is_active' => true,
                'badge' => 'Low Stock',
                'sales_count' => 64,
                'description' => 'Gaun pesta panjang dengan siluet leher halterneck berkerut mikro halus. Aksen plisket mewah memberikan ilusi tubuh semampai dan anggun di bawah pencahayaan malam.',
                'images' => ['images/product-3.png', 'images/product-1.png'],
                'video_url' => null,
            ],
            [
                'sku' => 'SKU-DRS-004',
                'name' => 'Noemi Minimalist Poplin Shirt Dress',
                'slug' => 'noemi-minimalist-poplin-shirt-dress',
                'category_id' => $categories['dresses']->id,
                'price' => 52.00,
                'original_price' => null,
                'stock' => 0, // Sold out
                'is_featured' => false,
                'is_active' => true,
                'badge' => 'Sold out',
                'sales_count' => 85,
                'description' => 'Kemeja gaun katun poplin organik berkerah klasik dengan saku samping tersembunyi. Siluet santai namun terstruktur untuk penampilan smart-casual dari kantor hingga weekend brunch.',
                'images' => ['images/product-4.png', 'images/product-5.png'],
                'video_url' => null,
            ],

            // ================= BLOUSES & TOPS =================
            [
                'sku' => 'SKU-TOP-001',
                'name' => 'Astrid Oversized French Linen Shirt',
                'slug' => 'astrid-oversized-french-linen-shirt',
                'category_id' => $categories['blouses-tops']->id,
                'price' => 38.00,
                'original_price' => 48.00,
                'stock' => 24,
                'is_featured' => true,
                'is_active' => true,
                'badge' => '-20%',
                'sales_count' => 165,
                'description' => 'Kemeja linen oversized dengan jahitan butik Indonesia yang rapi dan kancing mutiara asli. Mudah dipadukan sebagai atasan mandiri atau outer terbuka di atas tank top favorit Anda.',
                'images' => ['images/product-5.png', 'images/product-6.png'],
                'video_url' => null,
            ],
            [
                'sku' => 'SKU-TOP-002',
                'name' => 'Mireille Draped Satin Cowl Neck Top',
                'slug' => 'mireille-draped-satin-cowl-neck-top',
                'category_id' => $categories['blouses-tops']->id,
                'price' => 34.00,
                'original_price' => null,
                'stock' => 15,
                'is_featured' => true,
                'is_active' => true,
                'badge' => 'New Arrival',
                'sales_count' => 77,
                'description' => 'Atasan cowl neck berbahan satin sutra dengan kilau mewah yang elegan. Potongan tanpa lengan dengan kerah draped leher yang memberikan siluet leher jenjang dan feminin.',
                'images' => ['images/product-6.png', 'images/product-1.png'],
                'video_url' => null,
            ],
            [
                'sku' => 'SKU-TOP-003',
                'name' => 'Clara Cross-Wrap Peplum Blouse',
                'slug' => 'clara-cross-wrap-peplum-blouse',
                'category_id' => $categories['blouses-tops']->id,
                'price' => 42.00,
                'original_price' => 50.00,
                'stock' => 3, // Low stock
                'is_featured' => false,
                'is_active' => true,
                'badge' => 'Low Stock',
                'sales_count' => 52,
                'description' => 'Blouse model silang (wrap) dengan aksen peplum lembut di pinggang. Dibuat dari campuran katun rayon halus yang adem dan tidak mudah kusut sepanjang hari.',
                'images' => ['images/product-1.png', 'images/product-4.png'],
                'video_url' => null,
            ],
            [
                'sku' => 'SKU-TOP-004',
                'name' => 'Elise Ribbed Knit Halter Tank',
                'slug' => 'elise-ribbed-knit-halter-tank',
                'category_id' => $categories['blouses-tops']->id,
                'price' => 26.00,
                'original_price' => null,
                'stock' => 20,
                'is_featured' => false,
                'is_active' => true,
                'badge' => null,
                'sales_count' => 91,
                'description' => 'Tank top rajut ribbed katun lembut bertekstur elastis. Memberikan kenyamanan maksimal sebagai layering dasar di balik blazer atau dipadukan dengan celana kulot santai.',
                'images' => ['images/product-2.png', 'images/product-5.png'],
                'video_url' => null,
            ],

            // ================= OUTERWEAR & BLAZERS =================
            [
                'sku' => 'SKU-OUT-001',
                'name' => 'Valerie Tailored Double-Breasted Blazer',
                'slug' => 'valerie-tailored-double-breasted-blazer',
                'category_id' => $categories['outerwear-blazers']->id,
                'price' => 78.00,
                'original_price' => 98.00,
                'stock' => 14,
                'is_featured' => true,
                'is_active' => true,
                'badge' => 'Best Seller',
                'sales_count' => 120,
                'description' => 'Blazer formal struktural kancing ganda dengan bantalan bahu presisi dan kerah lapel tajam. Menggunakan furing sutra lembut di bagian dalam untuk kenyamanan berpakaian seharian.',
                'images' => ['images/product-3.png', 'images/product-4.png'],
                'video_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4',
            ],
            [
                'sku' => 'SKU-OUT-002',
                'name' => 'Helena Classic Relaxed Trench Coat',
                'slug' => 'helena-classic-relaxed-trench-coat',
                'category_id' => $categories['outerwear-blazers']->id,
                'price' => 110.00,
                'original_price' => 135.00,
                'stock' => 8,
                'is_featured' => true,
                'is_active' => true,
                'badge' => '-18%',
                'sales_count' => 63,
                'description' => 'Trench coat ikonis panjang selutut dari bahan katun gabardine tahan angin ringan. Dilengkapi sabuk pinggang bergesper tortoise shell dan flap badai di punggung.',
                'images' => ['images/product-4.png', 'images/product-6.png'],
                'video_url' => null,
            ],
            [
                'sku' => 'SKU-OUT-003',
                'name' => 'Simone Linen Summer Vest Blazer',
                'slug' => 'simone-linen-summer-vest-blazer',
                'category_id' => $categories['outerwear-blazers']->id,
                'price' => 46.00,
                'original_price' => null,
                'stock' => 17,
                'is_featured' => false,
                'is_active' => true,
                'badge' => 'Trending',
                'sales_count' => 49,
                'description' => 'Vest tanpa lengan berpotongan rapi dari linen murni. Memberikan tampilan profesional modern di iklim tropis yang tetap sejuk dan leluasa bergerak.',
                'images' => ['images/product-5.png', 'images/product-2.png'],
                'video_url' => null,
            ],

            // ================= PANTS & TROUSERS =================
            [
                'sku' => 'SKU-PNT-001',
                'name' => 'Paloma High-Waisted Wide Leg Trousers',
                'slug' => 'paloma-high-waisted-wide-leg-trousers',
                'category_id' => $categories['pants-trousers']->id,
                'price' => 48.00,
                'original_price' => 60.00,
                'stock' => 19,
                'is_featured' => true,
                'is_active' => true,
                'badge' => '-20%',
                'sales_count' => 138,
                'description' => 'Celana palazzo berpinggang tinggi dengan lipatan ploi depan (front pleats) yang tegas. Bahan stretch tencel-twill jatuh dengan anggun dan memberi siluet kaki jenjang.',
                'images' => ['images/product-6.png', 'images/product-3.png'],
                'video_url' => null,
            ],
            [
                'sku' => 'SKU-PNT-002',
                'name' => 'Vivienne Tailored Cigarette Ankle Pants',
                'slug' => 'vivienne-tailored-cigarette-ankle-pants',
                'category_id' => $categories['pants-trousers']->id,
                'price' => 45.00,
                'original_price' => null,
                'stock' => 11,
                'is_featured' => false,
                'is_active' => true,
                'badge' => null,
                'sales_count' => 74,
                'description' => 'Celana panjang pipa ramping dengan panjang pas di atas mata kaki. Dilengkapi ban pinggang elastis tersembunyi untuk kenyamanan duduk seharian di ruang kerja.',
                'images' => ['images/product-1.png', 'images/product-5.png'],
                'video_url' => null,
            ],
            [
                'sku' => 'SKU-PNT-003',
                'name' => 'Lara Drawstring Relaxed Linen Culottes',
                'slug' => 'lara-drawstring-relaxed-linen-culottes',
                'category_id' => $categories['pants-trousers']->id,
                'price' => 39.00,
                'original_price' => 49.00,
                'stock' => 1, // Critical low stock
                'is_featured' => false,
                'is_active' => true,
                'badge' => 'Low Stock',
                'sales_count' => 58,
                'description' => 'Kulot santai berbahan linen rami sejuk dengan tali serut elastis di pinggang. Pilihan sempurna untuk liburan santai tepi pantai atau akhir pekan santai.',
                'images' => ['images/product-2.png', 'images/product-6.png'],
                'video_url' => null,
            ],

            // ================= SKIRTS =================
            [
                'sku' => 'SKU-SKR-001',
                'name' => 'Diane Bias Cut Satin Midi Skirt',
                'slug' => 'diane-bias-cut-satin-midi-skirt',
                'category_id' => $categories['skirts']->id,
                'price' => 44.00,
                'original_price' => 55.00,
                'stock' => 22,
                'is_featured' => true,
                'is_active' => true,
                'badge' => '-20%',
                'sales_count' => 114,
                'description' => 'Rok midi satin berpotongan bias klasik yang mengalir anggun di setiap langkah. Padukan bersama sweater rajut kasmir untuk gaya effortless luxury.',
                'images' => ['images/product-3.png', 'images/product-1.png'],
                'video_url' => null,
            ],
            [
                'sku' => 'SKU-SKR-002',
                'name' => 'Giselle Accordion Pleated Maxi Skirt',
                'slug' => 'giselle-accordion-pleated-maxi-skirt',
                'category_id' => $categories['skirts']->id,
                'price' => 50.00,
                'original_price' => null,
                'stock' => 16,
                'is_featured' => false,
                'is_active' => true,
                'badge' => 'Trending',
                'sales_count' => 68,
                'description' => 'Rok panjang lipit akordeon halus dengan pinggang karet elastis dilapisi satin senada. Memberikan nuansa dramatis dan anggun saat menghadiri jamuan istimewa.',
                'images' => ['images/product-4.png', 'images/product-2.png'],
                'video_url' => null,
            ],

            // ================= KNITWEAR & SETS =================
            [
                'sku' => 'SKU-SET-001',
                'name' => 'Amelie Fine Cashmere Blend Cardigan Set',
                'slug' => 'amelie-fine-cashmere-blend-cardigan-set',
                'category_id' => $categories['knitwear-sets']->id,
                'price' => 85.00,
                'original_price' => 110.00,
                'stock' => 10,
                'is_featured' => true,
                'is_active' => true,
                'badge' => 'Gift Set',
                'sales_count' => 95,
                'description' => 'Satu set kardigan rajut halus kasmir beserta tank top senada. Terbuat dari benang kasmir ultra-ringan yang super lembut, hangat di ruangan ber-AC, dan tidak mudah berbulu.',
                'images' => ['images/product-5.png', 'images/product-3.png'],
                'video_url' => null,
            ],
            [
                'sku' => 'SKU-SET-002',
                'name' => 'Solange Linen Co-Ord Vest & Trousers Set',
                'slug' => 'solange-linen-co-ord-vest-and-trousers-set',
                'category_id' => $categories['knitwear-sets']->id,
                'price' => 92.00,
                'original_price' => 120.00,
                'stock' => 3, // Low stock
                'is_featured' => true,
                'is_active' => true,
                'badge' => 'Low Stock',
                'sales_count' => 81,
                'description' => 'Setelan co-ord dua potong (two-piece set) terdiri dari vest rompi kancing dan celana tailored palazzo berbahan linen murni warna oatmeal. Paduan gaya monokrom minimalis yang memikat.',
                'images' => ['images/product-6.png', 'images/product-4.png'],
                'video_url' => null,
            ],
            [
                'sku' => 'SKU-SET-003',
                'name' => 'Camille Soft Ribbed Lounge Knit Set',
                'slug' => 'camille-soft-ribbed-lounge-knit-set',
                'category_id' => $categories['knitwear-sets']->id,
                'price' => 64.00,
                'original_price' => 78.00,
                'stock' => 0, // Sold out
                'is_featured' => false,
                'is_active' => true,
                'badge' => 'Sold out',
                'sales_count' => 102,
                'description' => 'Setelan santai atasan lengan panjang dan celana kulot rajut berserat mikro. Menghadirkan kenyamanan mewah untuk bersantai di rumah maupun perjalanan liburan panjang.',
                'images' => ['images/product-1.png', 'images/product-2.png'],
                'video_url' => null,
            ],
        ];

        foreach ($products as $prodData) {
            $prodData['name_en'] = $prodData['name_en'] ?? $prodData['name'];
            $prodData['description_en'] = $prodData['description_en'] ?? $prodData['description'];

            if (! isset($prodData['size_stocks'])) {
                $stock = max(0, (int) ($prodData['stock'] ?? 0));
                if ($stock === 0) {
                    $prodData['size_stocks'] = ['S' => 0, 'M' => 0, 'L' => 0, 'XL' => 0];
                } else {
                    $q = (int) floor($stock / 4);
                    $rem = $stock % 4;
                    $prodData['size_stocks'] = [
                        'S' => $q + ($rem > 0 ? 1 : 0),
                        'M' => $q + ($rem > 1 ? 1 : 0),
                        'L' => $q + ($rem > 2 ? 1 : 0),
                        'XL' => $q,
                    ];
                }
            }

            Product::updateOrCreate(['sku' => $prodData['sku']], $prodData);
        }
    }
}
