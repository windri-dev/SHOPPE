<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductReview>
 */
class ProductReviewFactory extends Factory
{
    protected $model = ProductReview::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'user_id' => User::factory(),
            'customer_name' => fake()->name(),
            'customer_email' => fake()->safeEmail(),
            'rating' => fake()->numberBetween(4, 5),
            'size_purchased' => fake()->randomElement(['Size S', 'Size M', 'Size L', 'Size XL']),
            'headline' => fake()->sentence(5),
            'quality_tag' => fake()->randomElement(['Sangat bagus & adem', 'Sutra lembut', 'Katun adem']),
            'fit_tag' => fake()->randomElement(['Pas di badan', 'Sesuai ukuran']),
            'comment' => fake()->paragraph(),
            'images' => ['images/product-1.png'],
            'seller_reply' => 'Terima kasih atas ulasan dan kepercayaannya berbelanja di PUTRIJALVA!',
            'seller_replied_at' => now(),
            'is_verified_purchase' => true,
            'is_approved' => true,
            'helpful_count' => fake()->numberBetween(0, 20),
        ];
    }
}
