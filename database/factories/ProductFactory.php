<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = ucwords(fake()->unique()->words(3, true));

        return [
            'category_id' => Category::factory(),
            'sku' => 'SKU-'.strtoupper(Str::random(6)),
            'name' => $name,
            'slug' => Str::slug($name),
            'price' => fake()->randomFloat(2, 18, 120),
            'original_price' => null,
            'stock' => fake()->numberBetween(5, 40),
            'is_featured' => false,
            'is_active' => true,
            'badge' => null,
            'description' => fake()->paragraph(),
            'images' => ['images/product-1.png'],
            'sales_count' => fake()->numberBetween(0, 50),
        ];
    }
}
