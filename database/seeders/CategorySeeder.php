<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Dresses',
                'slug' => 'dresses',
                'description' => 'Midi slip dress, pleated wrap dresses, dan gaun kontemporer berbahan silk & linen pilihan untuk segala suasana.',
            ],
            [
                'name' => 'Blouses & Tops',
                'slug' => 'blouses-tops',
                'description' => 'Koleksi atasan wanita elegan, kemeja relaxed linen, satin drape blouse, dan kemeja katun berkancing.',
            ],
            [
                'name' => 'Outerwear & Blazers',
                'slug' => 'outerwear-blazers',
                'description' => 'Blazer berstruktur modern, oversized trench coat, vest santai, dan jaket tailored berkelas.',
            ],
            [
                'name' => 'Pants & Trousers',
                'slug' => 'pants-trousers',
                'description' => 'Celana palazzo jatuh berpinggang tinggi, tailored cigarette trousers, dan culottes santai yang nyaman.',
            ],
            [
                'name' => 'Skirts',
                'slug' => 'skirts',
                'description' => 'Rok midi bias-cut satin, pleated A-line skirt, dan wrap skirts yang anggun untuk gerak dinamis.',
            ],
            [
                'name' => 'Knitwear & Sets',
                'slug' => 'knitwear-sets',
                'description' => 'Setelan co-ord dua potong senada, sweater rajut kasmir ringan, dan lounge knitwear bernuansa tenang.',
            ],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['slug' => $cat['slug']], $cat);
        }
    }
}
