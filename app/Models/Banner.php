<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'title_en',
        'subtitle',
        'subtitle_en',
        'button_text',
        'button_text_en',
        'button_link',
        'image',
        'image_mobile',
        'image_position',
        'badge_text',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('id', 'desc');
    }

    public function getTitleAttribute(?string $value): string
    {
        $locale = app()->getLocale();
        if ($locale === 'en' && ! empty($this->attributes['title_en'])) {
            return $this->attributes['title_en'];
        }

        return $value ?? '';
    }

    public function getSubtitleAttribute(?string $value): ?string
    {
        $locale = app()->getLocale();
        if ($locale === 'en' && ! empty($this->attributes['subtitle_en'])) {
            return $this->attributes['subtitle_en'];
        }

        return $value;
    }

    public function getButtonTextAttribute(?string $value): string
    {
        $locale = app()->getLocale();
        if ($locale === 'en' && ! empty($this->attributes['button_text_en'])) {
            return $this->attributes['button_text_en'];
        }

        return $value ?: 'Lihat Koleksi';
    }

    public function getRawTitle(): string
    {
        return $this->attributes['title'] ?? '';
    }

    public function getRawSubtitle(): ?string
    {
        return $this->attributes['subtitle'] ?? null;
    }

    public function getImageUrlAttribute(): string
    {
        $img = $this->image;
        if (Str::startsWith($img, ['http://', 'https://'])) {
            return $img;
        }

        return asset($img);
    }

    public function getImageMobileUrlAttribute(): ?string
    {
        if (empty($this->image_mobile)) {
            return null;
        }

        if (Str::startsWith($this->image_mobile, ['http://', 'https://'])) {
            return $this->image_mobile;
        }

        return asset($this->image_mobile);
    }
}
