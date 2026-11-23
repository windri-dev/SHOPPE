<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'bank_name',
        'bank_code',
        'account_number',
        'account_holder',
        'branch',
        'qr_code_image',
        'instructions',
        'is_active',
        'sort_order',
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
        return $query->orderBy('sort_order', 'asc')->orderBy('id', 'asc');
    }

    public function getCleanAccountNumberAttribute(): string
    {
        return preg_replace('/[^0-9]/', '', (string) $this->account_number);
    }

    public function getBankColorAttribute(): string
    {
        $code = strtolower((string) ($this->bank_code ?: $this->bank_name));

        return match (true) {
            str_contains($code, 'bca') => '#003399',
            str_contains($code, 'mandiri') => '#003066',
            str_contains($code, 'bni') => '#FA5400',
            str_contains($code, 'bri') => '#00529C',
            str_contains($code, 'bsi') => '#00A39D',
            str_contains($code, 'seabank') => '#FF5722',
            default => '#374151',
        };
    }
}
