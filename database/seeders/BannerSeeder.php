<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $banners = [
            [
                'title' => 'Aurelia Ruched Silk Crop Top',
                'title_en' => 'Aurelia Ruched Silk Crop Top',
                'subtitle' => 'Rp 1.183.000',
                'subtitle_en' => '$ 65.00',
                'button_text' => 'Lihat Koleksi',
                'button_text_en' => 'Shop Collection',
                'button_link' => '/shop/aurelia-ruched-silk-crop-top',
                'image' => 'images/hero-1.png',
                'badge_text' => 'KOLEKSI UTAMA',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Valerie Tailored Linen Blazer Set',
                'title_en' => 'Valerie Tailored Linen Blazer Set',
                'subtitle' => 'Rp 1.729.000',
                'subtitle_en' => '$ 95.00',
                'button_text' => 'Buka Koleksi',
                'button_text_en' => 'Explore Set',
                'button_link' => '/shop/valerie-tailored-linen-blazer-crop-set',
                'image' => 'images/blog-1.jpg',
                'badge_text' => 'SETELAN EKSKLUSIF',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Bianca Cold-Shoulder Ribbed Crop',
                'title_en' => 'Bianca Cold-Shoulder Ribbed Crop',
                'subtitle' => 'Rp 764.000',
                'subtitle_en' => '$ 42.00',
                'button_text' => 'Eksplorasi Koleksi',
                'button_text_en' => 'Discover Top',
                'button_link' => '/shop/bianca-cold-shoulder-ribbed-crop-top',
                'image' => 'images/blog-2.jpg',
                'badge_text' => 'TERLARIS',
                'sort_order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($banners as $data) {
            Banner::updateOrCreate(
                ['title' => $data['title']],
                $data
            );
        }
    }
}
