<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $user_id
 * @property string $order_number
 * @property OrderStatus $status
 * @property float $subtotal
 * @property float $shipping_cost
 * @property float $discount_amount
 * @property float $grand_total
 * @property string $customer_name
 * @property string $customer_email
 * @property string $customer_phone
 * @property array<string, mixed> $shipping_address
 * @property string $payment_method
 * @property string $payment_status
 * @property string|null $payment_gateway
 * @property string|null $payment_reference
 * @property string|null $payment_channel
 * @property string|null $payment_url
 * @property string|null $payment_proof
 * @property Carbon|null $paid_at
 * @property string|null $tracking_number
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User|null $user
 * @property-read Collection<int, OrderItem> $items
 * @property-read Collection<int, PaymentTransaction> $transactions
 */
class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'subtotal',
        'shipping_cost',
        'discount_amount',
        'grand_total',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address',
        'payment_method',
        'payment_status',
        'payment_gateway',
        'payment_reference',
        'payment_channel',
        'payment_url',
        'payment_proof',
        'paid_at',
        'tracking_number',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'subtotal' => 'decimal:2',
            'shipping_cost' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'grand_total' => 'decimal:2',
            'shipping_address' => 'array',
            'paid_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function getFormattedGrandTotalAttribute(): string
    {
        return '$ '.number_format((float) $this->grand_total, 2);
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function isWaitingVerification(): bool
    {
        return $this->payment_status === 'waiting_verification';
    }

    public function paymentStatusLabel(): string
    {
        return match ($this->payment_status) {
            'paid' => 'Lunas',
            'waiting_verification' => 'Verifikasi Bukti',
            'refunded' => 'Dikembalikan (Refund)',
            default => 'Belum Dibayar',
        };
    }
}
