<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiKnowledgeEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'keywords',
        'content',
        'requires_human_handoff',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'requires_human_handoff' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'boundary' => 'Batasan AI (Guardrail)',
            'response_rule' => 'Aturan Respons Pelanggan',
            default => 'Pengetahuan Toko (Knowledge)',
        };
    }
}
