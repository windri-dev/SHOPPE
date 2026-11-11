<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'customer_name',
        'customer_email',
        'rating',
        'size_purchased',
        'headline',
        'comment',
        'quality_tag',
        'fit_tag',
        'images',
        'seller_reply',
        'seller_replied_at',
        'is_verified_purchase',
        'is_approved',
        'helpful_count',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'images' => 'array',
            'seller_replied_at' => 'datetime',
            'is_verified_purchase' => 'boolean',
            'is_approved' => 'boolean',
            'helpful_count' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('is_approved', true);
    }

    public function getInitialsAttribute(): string
    {
        $words = explode(' ', trim($this->customer_name));
        $initials = '';
        foreach (array_slice($words, 0, 2) as $w) {
            $initials .= mb_strtoupper(mb_substr($w, 0, 1));
        }

        return $initials !== '' ? $initials : 'PJ';
    }

    public function getDisplayNameAttribute(): string
    {
        return trim($this->customer_name);
    }

    public function getHasMediaAttribute(): bool
    {
        return is_array($this->images) && count($this->images) > 0;
    }

    public function getHasCommentAttribute(): bool
    {
        return ! empty(trim((string) $this->comment));
    }
}
