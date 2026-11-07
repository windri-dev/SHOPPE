<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Earrings', 'slug' => 'earrings', 'description' => 'Sculptural and minimalist drop, huggie, and hoop earrings.'],
            ['name' => 'Necklaces', 'slug' => 'necklaces', 'description' => 'Delicate chains, pendants, and layering statement pieces.'],
            ['name' => 'Hair pins', 'slug' => 'hairpins', 'description' => 'Artisan brass hair pins and botanical clip ensembles.'],
            ['name' => 'Rings', 'slug' => 'rings', 'description' => 'Architectural bands and polished signet rings.'],
            ['name' => 'Bracelets', 'slug' => 'bracelets', 'description' => 'Fine wrist cuffs and minimalist chain links.'],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['slug' => $cat['slug']], $cat);
        }
    }
}
