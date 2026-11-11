<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Inquiry;
use App\Models\InquiryMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InquiryMessage>
 */
class InquiryMessageFactory extends Factory
{
    protected $model = InquiryMessage::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'inquiry_id' => Inquiry::factory(),
            'sender_type' => fake()->randomElement(['user', 'staff', 'bot']),
            'message' => fake()->paragraph(),
            'created_at' => now(),
        ];
    }
}
