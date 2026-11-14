<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\CurrencyService;
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
        'name_en',
        'slug',
        'price',
        'original_price',
        'stock',
        'size_stocks',
        'is_featured',
        'is_active',
        'badge',
        'description',
        'description_en',
        'images',
        'video_url',
        'sales_count',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'original_price' => 'decimal:2',
            'stock' => 'integer',
            'size_stocks' => 'array',
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
            $img = $this->images[0];
            if (str_starts_with($img, 'http://') || str_starts_with($img, 'https://') || str_starts_with($img, 'storage/')) {
                return $img;
            }
            if (file_exists(public_path($img))) {
                return $img;
            }

            return 'storage/'.$img;
        }

        return 'images/product-1.png';
    }

    public function getFirstImageUrlAttribute(): string
    {
        $primary = $this->primary_image;
        if (str_starts_with($primary, 'http://') || str_starts_with($primary, 'https://')) {
            return $primary;
        }

        return asset($primary);
    }

    /**
     * Dapatkan daftar URL seluruh gambar produk yang siap ditampilkan.
     *
     * @return array<int, string>
     */
    public function getGalleryImageUrlsAttribute(): array
    {
        if (! is_array($this->images) || empty($this->images)) {
            return [asset('images/product-1.png')];
        }

        return array_map(function (string $img): string {
            if (str_starts_with($img, 'http://') || str_starts_with($img, 'https://')) {
                return $img;
            }
            if (str_starts_with($img, 'storage/')) {
                return asset($img);
            }
            if (file_exists(public_path($img))) {
                return asset($img);
            }

            return asset('storage/'.$img);
        }, $this->images);
    }

    public function getFormattedPriceAttribute(): string
    {
        return app(CurrencyService::class)->format((float) $this->price);
    }

    public function getPriceFormattedAttribute(): string
    {
        return $this->formatted_price;
    }

    public function getFormattedOriginalPriceAttribute(): ?string
    {
        return $this->original_price !== null
            ? app(CurrencyService::class)->format((float) $this->original_price)
            : null;
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

    public function getNameAttribute(?string $value): string
    {
        if (app()->getLocale() === 'en' && ! empty($this->attributes['name_en'])) {
            return (string) $this->attributes['name_en'];
        }

        return $value ?? '';
    }

    public function getDescriptionAttribute(?string $value): ?string
    {
        if (app()->getLocale() === 'en' && ! empty($this->attributes['description_en'])) {
            return (string) $this->attributes['description_en'];
        }

        return $value;
    }

    public function getRawName(): string
    {
        return $this->attributes['name'] ?? '';
    }

    public function getRawDescription(): ?string
    {
        return $this->attributes['description'] ?? null;
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

    public function getVideoUrlAssetAttribute(): ?string
    {
        if (empty($this->video_url)) {
            return null;
        }

        if (str_starts_with($this->video_url, 'http://') || str_starts_with($this->video_url, 'https://')) {
            return $this->video_url;
        }

        if (str_starts_with($this->video_url, 'storage/')) {
            return asset($this->video_url);
        }

        if (file_exists(public_path($this->video_url))) {
            return asset($this->video_url);
        }

        return asset('storage/'.$this->video_url);
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

        return $this->video_url_asset;
    }

    /**
     * @return HasMany<Wishlist, Product>
     */
    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function isWishlistedBy(?User $user): bool
    {
        if (! $user) {
            $sessionWishlist = session('wishlist', []);

            return in_array($this->id, $sessionWishlist, true);
        }

        if ($this->relationLoaded('wishlists')) {
            return $this->wishlists->contains('user_id', $user->id);
        }

        return $this->wishlists()->where('user_id', $user->id)->exists();
    }

    /**
     * Dapatkan matriks kuota stok per ukuran pakaian (S, M, L, XL).
     *
     * @return array<string, int>
     */
    public function getSizeStocksAttribute(): array
    {
        $stocks = $this->attributes['size_stocks'] ?? null;
        if ($stocks !== null) {
            $decoded = is_string($stocks) ? json_decode($stocks, true) : $stocks;
            if (is_array($decoded) && count($decoded) > 0) {
                return array_map('intval', $decoded);
            }
        }

        $total = max(0, (int) ($this->attributes['stock'] ?? 0));
        $quarter = (int) floor($total / 4);
        $remainder = $total % 4;

        return [
            'S' => $quarter + ($remainder > 0 ? 1 : 0),
            'M' => $quarter + ($remainder > 1 ? 1 : 0),
            'L' => $quarter + ($remainder > 2 ? 1 : 0),
            'XL' => $quarter,
        ];
    }

    public function getStockForSize(string $size): int
    {
        $normalized = strtoupper(trim($size));
        $matrix = $this->size_stocks;

        return (int) ($matrix[$normalized] ?? 0);
    }

    public function hasStockForSize(string $size, int $qty = 1): bool
    {
        return $this->getStockForSize($size) >= max(1, $qty);
    }

    public function decrementSizeStock(string $size, int $qty = 1): bool
    {
        $normalized = strtoupper(trim($size));
        $matrix = $this->size_stocks;
        $current = (int) ($matrix[$normalized] ?? 0);

        if ($current < $qty) {
            return false;
        }

        $matrix[$normalized] = $current - $qty;
        $this->size_stocks = $matrix;
        $this->stock = array_sum($matrix);
        $this->save();

        return true;
    }
}
