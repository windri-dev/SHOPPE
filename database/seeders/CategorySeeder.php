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
                'name' => 'Earrings',
                'slug' => 'earrings',
                'description' => 'Sculptural and minimalist drop, huggie, and hoop earrings cast in 18k gold-dipped brass.',
            ],
            [
                'name' => 'Necklaces',
                'slug' => 'necklaces',
                'description' => 'Delicate chains, statement pendants, and artisanal layering pieces crafted for daily elegance.',
            ],
            [
                'name' => 'Hair pins',
                'slug' => 'hairpins',
                'description' => 'Artisan brass hair pins and botanical clip ensembles designed for effortless hair styling.',
            ],
            [
                'name' => 'Rings',
                'slug' => 'rings',
                'description' => 'Architectural bands, pavé stackables, and polished signet rings sculpted for modern hands.',
            ],
            [
                'name' => 'Bracelets',
                'slug' => 'bracelets',
                'description' => 'Fine wrist cuffs, tennis chains, and minimalist cable links made from recycled precious metals.',
            ],
            [
                'name' => 'Pendants & Charms',
                'slug' => 'pendants',
                'description' => 'Intricately engraved talismans, celestial motifs, and organic Baroque pearl pendants.',
            ],
            [
                'name' => 'Brooches & Pins',
                'slug' => 'brooches',
                'description' => 'Contemporary sculptural lapel brooches adding elevated distinction to coats and blouses.',
            ],
            [
                'name' => 'Anklets',
                'slug' => 'anklets',
                'description' => 'Sun-kissed delicate ankle chains designed for warm seasons and coastal getaways.',
            ],
            [
                'name' => 'Artisan Watches',
                'slug' => 'watches',
                'description' => 'Minimalist mechanical timepieces featuring mesh bracelets and sapphire crystal glass.',
            ],
            [
                'name' => 'Gift Sets',
                'slug' => 'sets',
                'description' => 'Curated matching jewelry capsules presented in signature velvet keepsake boxes.',
            ],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['slug' => $cat['slug']], $cat);
        }
    }
}
