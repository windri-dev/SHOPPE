<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\InquiryStatus;
use App\Models\Inquiry;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Inquiry>
 */
class InquiryFactory extends Factory
{
    protected $model = Inquiry::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ticket_number' => 'TCK-'.strtoupper(Str::random(6)),
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => '0812'.fake()->numerify('########'),
            'subject' => fake()->sentence(4),
            'channel' => 'livechat',
            'status' => InquiryStatus::UNREAD,
        ];
    }
}
