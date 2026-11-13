<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wishlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
    ];

    /**
     * Sinkronisasi produk wishlist dari sesi tamu ke database pengguna setelah login.
     */
    public static function syncGuestSessionToDatabase(int $userId): void
    {
        $guestItems = (array) session('wishlist', []);
        if (empty($guestItems)) {
            return;
        }

        $validProductIds = Product::whereIn('id', $guestItems)->pluck('id');

        foreach ($validProductIds as $productId) {
            static::firstOrCreate([
                'user_id' => $userId,
                'product_id' => $productId,
            ]);
        }

        session()->forget('wishlist');
    }

    /**
     * @return BelongsTo<User, Wishlist>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Product, Wishlist>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
