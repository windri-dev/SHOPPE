<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string|null $username
 * @property string $email
 * @property string|null $phone
 * @property string|null $gender
 * @property Carbon|null $birth_date
 * @property string|null $address
 * @property string|null $country
 * @property string|null $province
 * @property string|null $city
 * @property string|null $district
 * @property string|null $postcode
 * @property string|null $address_label
 * @property string|null $address_notes
 * @property array<string, mixed>|null $saved_addresses
 * @property array<string, mixed>|null $bank_account
 * @property array<string, mixed>|null $notification_preferences
 * @property UserRole $role
 * @property Carbon|null $email_verified_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'phone',
        'gender',
        'birth_date',
        'address',
        'country',
        'province',
        'city',
        'district',
        'postcode',
        'address_label',
        'address_notes',
        'saved_addresses',
        'bank_account',
        'notification_preferences',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'birth_date' => 'date',
            'password' => 'hashed',
            'role' => UserRole::class,
            'saved_addresses' => 'array',
            'bank_account' => 'array',
            'notification_preferences' => 'array',
        ];
    }

    public function getFirstNameAttribute(): string
    {
        $parts = preg_split('/\s+/', trim((string) $this->name), 2);

        return $parts[0] ?? '';
    }

    public function getLastNameAttribute(): string
    {
        $parts = preg_split('/\s+/', trim((string) $this->name), 2);

        return $parts[1] ?? '-';
    }

    /**
     * Dapatkan daftar buku alamat lengkap (menggabungkan alamat utama jika saved_addresses kosong).
     *
     * @return array<int, array<string, mixed>>
     */
    public function getAddressBookAttribute(): array
    {
        $saved = is_array($this->saved_addresses) ? $this->saved_addresses : [];

        if (count($saved) > 0) {
            return $saved;
        }

        if (! empty($this->address)) {
            return [
                [
                    'id' => 'primary-1',
                    'label' => $this->address_label ?: 'Rumah',
                    'recipient_name' => $this->name,
                    'phone' => $this->phone ?? '',
                    'country' => $this->country ?: 'Indonesia',
                    'province' => $this->province ?? '',
                    'city' => $this->city ?? '',
                    'district' => $this->district ?? '',
                    'postcode' => $this->postcode ?? '',
                    'street_address' => $this->address,
                    'notes' => $this->address_notes ?? '',
                    'is_primary' => true,
                ],
            ];
        }

        return [];
    }

    public function isAdmin(): bool
    {
        if ($this->role instanceof UserRole) {
            return $this->role->isAdmin();
        }

        return in_array($this->role, ['admin', 'store_admin', 'store_staff'], true);
    }

    public function isUser(): bool
    {
        if ($this->role instanceof UserRole) {
            return $this->role->isUser();
        }

        return in_array($this->role, ['user', 'customer'], true);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function inquiries(): HasMany
    {
        return $this->hasMany(Inquiry::class);
    }
}
