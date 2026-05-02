<?php

namespace App\Enums;

enum AppStatus: string
{
    case Aktif = 'aktif';
    case Idle = 'idle';
    case Nonaktif = 'nonaktif';

    public function label(): string
    {
        return match ($this) {
            self::Aktif => 'Aktif',
            self::Idle => 'Idle',
            self::Nonaktif => 'Nonaktif',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
