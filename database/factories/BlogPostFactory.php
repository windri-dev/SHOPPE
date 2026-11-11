<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\BlogPost;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<BlogPost>
 */
class BlogPostFactory extends Factory
{
    protected $model = BlogPost::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = ucwords(fake()->unique()->sentence(5));

        return [
            'title' => $title,
            'title_en' => $title,
            'slug' => Str::slug($title),
            'category' => 'Fashion',
            'author' => fake()->name(),
            'image' => 'images/blog-1.jpg',
            'excerpt' => fake()->sentence(12),
            'excerpt_en' => fake()->sentence(12),
            'content' => fake()->paragraphs(3, true),
            'content_en' => fake()->paragraphs(3, true),
            'published_at' => now(),
        ];
    }
}
