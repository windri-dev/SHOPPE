<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'sku',
        'name',
        'slug',
        'price',
        'original_price',
        'stock',
        'is_featured',
        'is_active',
        'badge',
        'description',
        'images',
        'sales_count',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'original_price' => 'decimal:2',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'images' => 'array',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true)->where('is_active', true);
    }

    public function getPrimaryImageAttribute(): string
    {
        if (is_array($this->images) && count($this->images) > 0) {
            return $this->images[0];
        }
        return 'images/product-1.png';
    }

    public function getFormattedPriceAttribute(): string
    {
        return '$ ' . number_format($this->price, 2);
    }

    public function getFormattedOriginalPriceAttribute(): ?string
    {
        return $this->original_price ? '$ ' . number_format($this->original_price, 2) : null;
    }

    public function isInStock(): bool
    {
        return $this->stock > 0;
    }
}
