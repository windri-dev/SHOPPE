<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\AiKnowledgeEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AiKnowledgeEntry>
 */
class AiKnowledgeEntryFactory extends Factory
{
    protected $model = AiKnowledgeEntry::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => 'knowledge',
            'title' => ucwords(fake()->words(3, true)),
            'keywords' => implode(', ', fake()->words(4)),
            'content' => fake()->paragraph(),
            'requires_human_handoff' => false,
            'is_active' => true,
        ];
    }
}

