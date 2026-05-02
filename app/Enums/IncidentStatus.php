<?php

namespace App\Enums;

enum IncidentStatus: string
{
    case Baru = 'Baru';
    case Diverifikasi = 'Diverifikasi';
    case DalamPenyelidikan = 'Dalam Penyelidikan';
    case Selesai = 'Selesai';
    case Ditutup = 'Ditutup';

    /** @return string[] */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return $this->value;
    }

    public function isClosed(): bool
    {
        return $this === self::Ditutup;
    }
}
