<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $order_id
 * @property string $provider
 * @property string $transaction_id
 * @property string|null $payment_channel
 * @property float $amount
 * @property string $status
 * @property string|null $signature
 * @property array<string, mixed>|null $payload_response
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Order $order
 */
class PaymentTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'provider',
        'transaction_id',
        'payment_channel',
        'amount',
        'status',
        'signature',
        'payload_response',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payload_response' => 'array',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
