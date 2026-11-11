<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'video_url',
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

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function approvedReviews(): HasMany
    {
        return $this->hasMany(ProductReview::class)->where('is_approved', true)->latest();
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

    public function getFirstImageUrlAttribute(): string
    {
        return asset($this->primary_image);
    }

    public function getFormattedPriceAttribute(): string
    {
        return '$ '.number_format((float) $this->price, 2);
    }

    public function getPriceFormattedAttribute(): string
    {
        return $this->formatted_price;
    }

    public function getFormattedOriginalPriceAttribute(): ?string
    {
        return $this->original_price ? '$ '.number_format((float) $this->original_price, 2) : null;
    }

    public function getOriginalPriceFormattedAttribute(): ?string
    {
        return $this->formatted_original_price;
    }

    public function getAverageRatingAttribute(): float
    {
        $avg = $this->relationLoaded('approvedReviews')
            ? $this->approvedReviews->avg('rating')
            : $this->approvedReviews()->avg('rating');

        return $avg !== null ? round((float) $avg, 1) : 5.0;
    }

    public function getReviewsCountAttribute(): int
    {
        return $this->relationLoaded('approvedReviews')
            ? $this->approvedReviews->count()
            : $this->approvedReviews()->count();
    }

    public function getReviewsWithCommentCountAttribute(): int
    {
        $reviews = $this->relationLoaded('approvedReviews') ? $this->approvedReviews : $this->approvedReviews()->get();

        return $reviews->filter(fn ($r) => ! empty(trim((string) $r->comment)))->count();
    }

    public function getReviewsWithMediaCountAttribute(): int
    {
        $reviews = $this->relationLoaded('approvedReviews') ? $this->approvedReviews : $this->approvedReviews()->get();

        return $reviews->filter(fn ($r) => is_array($r->images) && count($r->images) > 0)->count();
    }

    /**
     * @return array<int, array{count: int, percentage: int}>
     */
    public function getRatingBreakdownAttribute(): array
    {
        $reviews = $this->relationLoaded('approvedReviews')
            ? $this->approvedReviews
            : $this->approvedReviews()->get();

        $total = $reviews->count();
        $breakdown = [];

        for ($star = 5; $star >= 1; $star--) {
            $count = $reviews->where('rating', $star)->count();
            $percentage = $total > 0 ? (int) round(($count / $total) * 100) : 0;
            $breakdown[$star] = [
                'count' => $count,
                'percentage' => $percentage,
            ];
        }

        return $breakdown;
    }

    public function getRecommendationPercentageAttribute(): int
    {
        $reviews = $this->relationLoaded('approvedReviews')
            ? $this->approvedReviews
            : $this->approvedReviews()->get();

        $total = $reviews->count();
        if ($total === 0) {
            return 100;
        }

        $favorable = $reviews->where('rating', '>=', 4)->count();

        return (int) round(($favorable / $total) * 100);
    }

    public function isInStock(): bool
    {
        return $this->stock > 0;
    }

    public function hasVideo(): bool
    {
        return ! empty($this->video_url);
    }

    public function getVideoTypeAttribute(): string
    {
        if (empty($this->video_url)) {
            return 'none';
        }

        if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([\w\-]+)/i', $this->video_url)) {
            return 'youtube';
        }

        if (preg_match('/vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/[^\/]*\/videos\/|album\/(?:\d+\/)?video\/|video\/|)(\d+)/i', $this->video_url)) {
            return 'vimeo';
        }

        return 'direct';
    }

    public function getVideoEmbedUrlAttribute(): ?string
    {
        if (empty($this->video_url)) {
            return null;
        }

        if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([\w\-]+)/i', $this->video_url, $matches)) {
            return 'https://www.youtube.com/embed/'.$matches[1].'?autoplay=0&rel=0&modestbranding=1';
        }

        if (preg_match('/vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/[^\/]*\/videos\/|album\/(?:\d+\/)?video\/|video\/|)(\d+)/i', $this->video_url, $matches)) {
            return 'https://player.vimeo.com/video/'.$matches[1];
        }

        return $this->video_url;
    }
}
