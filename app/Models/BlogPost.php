<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'title_en',
        'slug',
        'category',
        'excerpt',
        'excerpt_en',
        'content',
        'content_en',
        'image',
        'author',
        'published_at',
    ];

    /**
     * Dapatkan judul artikel sesuai locale aktif.
     */
    public function getTitleAttribute(?string $value): ?string
    {
        if (app()->getLocale() === 'en' && ! empty($this->attributes['title_en'])) {
            return $this->attributes['title_en'];
        }

        return $value;
    }

    /**
     * Dapatkan ringkasan kutipan artikel sesuai locale aktif.
     */
    public function getExcerptAttribute(?string $value): ?string
    {
        if (app()->getLocale() === 'en' && ! empty($this->attributes['excerpt_en'])) {
            return $this->attributes['excerpt_en'];
        }

        return $value;
    }

    /**
     * Dapatkan isi lengkap artikel sesuai locale aktif.
     */
    public function getContentAttribute(?string $value): ?string
    {
        if (app()->getLocale() === 'en' && ! empty($this->attributes['content_en'])) {
            return $this->attributes['content_en'];
        }

        return $value;
    }

    public function getRawTitle(): ?string
    {
        return $this->attributes['title'] ?? null;
    }

    public function getRawExcerpt(): ?string
    {
        return $this->attributes['excerpt'] ?? null;
    }

    public function getRawContent(): ?string
    {
        return $this->attributes['content'] ?? null;
    }

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }
}
