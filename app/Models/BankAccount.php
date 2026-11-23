<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $bank_name
 * @property string|null $bank_code
 * @property string $account_number
 * @property string $account_holder
 * @property string|null $branch
 * @property string|null $qr_code_image
 * @property string|null $instructions
 * @property bool $is_active
 * @property int $sort_order
 * @property-read string $clean_account_number
 * @property-read string $bank_color
 * @property-read string $bank_bg_color
 * @property-read string $bank_icon_svg
 */
class BankAccount extends Model
{
    use HasFactory;

    public const PRESET_BANKS = [
        'bca' => [
            'name' => 'BCA (Bank Central Asia)',
            'short_name' => 'BCA',
            'code' => 'bca',
            'color' => '#003399',
            'bg_color' => '#EFF6FF',
        ],
        'mandiri' => [
            'name' => 'Bank Mandiri',
            'short_name' => 'Mandiri',
            'code' => 'mandiri',
            'color' => '#003066',
            'bg_color' => '#F0F4F8',
        ],
        'bni' => [
            'name' => 'Bank BNI (Bank Negara Indonesia)',
            'short_name' => 'BNI',
            'code' => 'bni',
            'color' => '#FA5400',
            'bg_color' => '#FFF7ED',
        ],
        'bri' => [
            'name' => 'Bank BRI (Bank Rakyat Indonesia)',
            'short_name' => 'BRI',
            'code' => 'bri',
            'color' => '#00529C',
            'bg_color' => '#EFF6FF',
        ],
        'bsi' => [
            'name' => 'Bank Syariah Indonesia (BSI)',
            'short_name' => 'BSI',
            'code' => 'bsi',
            'color' => '#00A39D',
            'bg_color' => '#F0FDF4',
        ],
        'cimb' => [
            'name' => 'CIMB Niaga',
            'short_name' => 'CIMB',
            'code' => 'cimb',
            'color' => '#ED1C24',
            'bg_color' => '#FEF2F2',
        ],
        'permata' => [
            'name' => 'Bank Permata',
            'short_name' => 'Permata',
            'code' => 'permata',
            'color' => '#84BD00',
            'bg_color' => '#F7FEE7',
        ],
        'danamon' => [
            'name' => 'Bank Danamon',
            'short_name' => 'Danamon',
            'code' => 'danamon',
            'color' => '#F15A24',
            'bg_color' => '#FFF7ED',
        ],
        'jago' => [
            'name' => 'Bank Jago',
            'short_name' => 'Jago',
            'code' => 'jago',
            'color' => '#FF7F00',
            'bg_color' => '#FFFBEB',
        ],
        'seabank' => [
            'name' => 'SeaBank Indonesia',
            'short_name' => 'SeaBank',
            'code' => 'seabank',
            'color' => '#FF5722',
            'bg_color' => '#FFF7ED',
        ],
        'btn' => [
            'name' => 'Bank BTN',
            'short_name' => 'BTN',
            'code' => 'btn',
            'color' => '#1C3F94',
            'bg_color' => '#EFF6FF',
        ],
        'jenius' => [
            'name' => 'Jenius (BTPN)',
            'short_name' => 'Jenius',
            'code' => 'jenius',
            'color' => '#00A4E4',
            'bg_color' => '#F0F9FF',
        ],
    ];

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
            str_contains($code, 'cimb') => '#ED1C24',
            str_contains($code, 'permata') => '#84BD00',
            str_contains($code, 'danamon') => '#F15A24',
            str_contains($code, 'jago') => '#FF7F00',
            str_contains($code, 'seabank') => '#FF5722',
            str_contains($code, 'btn') => '#1C3F94',
            str_contains($code, 'jenius') || str_contains($code, 'btpn') => '#00A4E4',
            default => '#374151',
        };
    }

    public function getBankBgColorAttribute(): string
    {
        $code = strtolower((string) ($this->bank_code ?: $this->bank_name));

        return match (true) {
            str_contains($code, 'bca') || str_contains($code, 'bri') || str_contains($code, 'btn') => '#EFF6FF',
            str_contains($code, 'mandiri') => '#F0F4F8',
            str_contains($code, 'bni') || str_contains($code, 'danamon') || str_contains($code, 'seabank') => '#FFF7ED',
            str_contains($code, 'bsi') => '#F0FDF4',
            str_contains($code, 'cimb') => '#FEF2F2',
            str_contains($code, 'permata') => '#F7FEE7',
            str_contains($code, 'jago') => '#FFFBEB',
            str_contains($code, 'jenius') => '#F0F9FF',
            default => '#F3F4F6',
        };
    }

    /**
     * Get SVG icon or stylized badge for this bank.
     */
    public function getBankIconSvgAttribute(): string
    {
        $code = strtolower((string) ($this->bank_code ?: $this->bank_name));
        $color = $this->bank_color;

        return match (true) {
            str_contains($code, 'bca') => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none"><rect width="24" height="24" rx="5" fill="#003399"/><text x="12" y="16" fill="#FFFFFF" font-family="Arial,sans-serif" font-weight="900" font-size="9" text-anchor="middle" letter-spacing="-0.5">BCA</text></svg>',
            str_contains($code, 'mandiri') => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none"><rect width="24" height="24" rx="5" fill="#003066"/><path d="M4 14C8 10 16 10 20 14" stroke="#FDB913" stroke-width="2.5" stroke-linecap="round"/><text x="12" y="11" fill="#FFFFFF" font-family="Arial,sans-serif" font-weight="800" font-size="6.5" text-anchor="middle">MANDIRI</text></svg>',
            str_contains($code, 'bni') => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none"><rect width="24" height="24" rx="5" fill="#FA5400"/><text x="12" y="16" fill="#FFFFFF" font-family="Arial,sans-serif" font-weight="900" font-size="9" text-anchor="middle">BNI</text></svg>',
            str_contains($code, 'bri') => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none"><rect width="24" height="24" rx="5" fill="#00529C"/><text x="12" y="16" fill="#FFFFFF" font-family="Arial,sans-serif" font-weight="900" font-size="9" text-anchor="middle">BRI</text></svg>',
            str_contains($code, 'bsi') => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none"><rect width="24" height="24" rx="5" fill="#00A39D"/><circle cx="12" cy="7" r="2" fill="#EAAA00"/><text x="12" y="18" fill="#FFFFFF" font-family="Arial,sans-serif" font-weight="900" font-size="8" text-anchor="middle">BSI</text></svg>',
            str_contains($code, 'cimb') => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none"><rect width="24" height="24" rx="5" fill="#ED1C24"/><text x="12" y="16" fill="#FFFFFF" font-family="Arial,sans-serif" font-weight="900" font-size="8" text-anchor="middle">CIMB</text></svg>',
            str_contains($code, 'seabank') => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none"><rect width="24" height="24" rx="5" fill="#FF5722"/><text x="12" y="15" fill="#FFFFFF" font-family="Arial,sans-serif" font-weight="800" font-size="7" text-anchor="middle">Sea</text></svg>',
            str_contains($code, 'jago') => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none"><rect width="24" height="24" rx="5" fill="#FF7F00"/><text x="12" y="16" fill="#FFFFFF" font-family="Arial,sans-serif" font-weight="900" font-size="7.5" text-anchor="middle">JAGO</text></svg>',
            str_contains($code, 'permata') => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none"><rect width="24" height="24" rx="5" fill="#84BD00"/><polygon points="12,5 18,12 12,19 6,12" fill="#FFFFFF"/></svg>',
            str_contains($code, 'danamon') => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none"><rect width="24" height="24" rx="5" fill="#F15A24"/><line x1="6" y1="12" x2="18" y2="12" stroke="#FFD100" stroke-width="3"/><line x1="6" y1="8" x2="18" y2="8" stroke="#FFFFFF" stroke-width="2"/><line x1="6" y1="16" x2="18" y2="16" stroke="#FFFFFF" stroke-width="2"/></svg>',
            str_contains($code, 'btn') => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none"><rect width="24" height="24" rx="5" fill="#1C3F94"/><text x="12" y="16" fill="#FFFFFF" font-family="Arial,sans-serif" font-weight="900" font-size="8.5" text-anchor="middle">BTN</text></svg>',
            str_contains($code, 'jenius') => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none"><rect width="24" height="24" rx="5" fill="#00A4E4"/><circle cx="12" cy="12" r="6" stroke="#FFFFFF" stroke-width="2.5"/></svg>',
            default => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="'.$color.'" stroke-width="2"><rect x="3" y="5" width="18" height="14" rx="3"/><line x1="3" y1="10" x2="21" y2="10"/></svg>',
        };
    }
}
