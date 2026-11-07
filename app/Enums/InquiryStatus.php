<?php

namespace App\Enums;

enum InquiryStatus: string
{
    case UNREAD = 'unread';
    case RESPONDED = 'responded';
    case RESOLVED = 'resolved';

    public function label(): string
    {
        return match ($this) {
            self::UNREAD => 'Belum Dibaca',
            self::RESPONDED => 'Sudah Dibalas',
            self::RESOLVED => 'Selesai',
        };
    }
}
