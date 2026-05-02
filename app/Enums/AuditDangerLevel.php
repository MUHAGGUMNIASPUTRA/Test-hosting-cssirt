<?php

namespace App\Enums;

enum AuditDangerLevel: string
{
    case Bahaya = 'bahaya';
    case Peringatan = 'peringatan';
    case Aman = 'aman';

    public function label(): string
    {
        return match ($this) {
            self::Bahaya => 'Bahaya',
            self::Peringatan => 'Peringatan',
            self::Aman => 'Aman',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
