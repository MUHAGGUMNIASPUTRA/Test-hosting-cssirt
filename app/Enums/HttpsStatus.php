<?php

namespace App\Enums;

enum HttpsStatus: string
{
    case Aktif = 'aktif';
    case Expired = 'expired';
    case Nonaktif = 'nonaktif';

    public function label(): string
    {
        return match ($this) {
            self::Aktif => 'Aktif',
            self::Expired => 'Expired',
            self::Nonaktif => 'Nonaktif',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
