<?php

namespace App\Enums;

enum AnnouncementLevel: string
{
    case Info = 'info';
    case Warning = 'warning';
    case Critical = 'critical';

    /** @return string[] */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::Info => 'Info',
            self::Warning => 'Peringatan',
            self::Critical => 'Kritis',
        };
    }
}
