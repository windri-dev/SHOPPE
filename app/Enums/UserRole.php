<?php

namespace App\Enums;

enum UserRole: string
{
    case CUSTOMER = 'customer';
    case USER = 'user';
    case STORE_ADMIN = 'store_admin';
    case STORE_STAFF = 'store_staff';
    case ADMIN = 'admin';

    public function isAdmin(): bool
    {
        return in_array($this, [self::STORE_ADMIN, self::STORE_STAFF, self::ADMIN], true);
    }

    public function isUser(): bool
    {
        return in_array($this, [self::CUSTOMER, self::USER], true);
    }

    public function label(): string
    {
        return match ($this) {
            self::CUSTOMER => 'Pelanggan Umum',
            self::USER => 'User / Pengguna',
            self::STORE_ADMIN => 'Store Administrator',
            self::STORE_STAFF => 'Store Staff / Operator',
            self::ADMIN => 'Administrator',
        };
    }
}
