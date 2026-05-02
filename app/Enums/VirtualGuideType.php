<?php

namespace App\Enums;

enum VirtualGuideType: string
{
    case Web = 'web';
    case Mobile = 'mobile';

    public function label(): string
    {
        return match ($this) {
            self::Web => 'Aplikasi Web',
            self::Mobile => 'Aplikasi Mobile',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
