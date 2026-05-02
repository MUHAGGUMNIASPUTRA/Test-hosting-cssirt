<?php

namespace App\Enums;

enum DocumentStage: string
{
    case PerluDibuat = 'Perlu Dibuat';
    case TelahDibuat = 'Telah Dibuat';
    case PerluReview = 'Perlu Review';
    case TelahDireview = 'Telah Direview';
    case PerluTTD = 'Perlu TTD';
    case Final = 'Final';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
