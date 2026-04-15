<?php

namespace App\Enums;

enum AssetStage: string
{
    case Draft = 'draft';
    case Pengajuan = 'pengajuan';
    case Pengujian = 'pengujian';
    case Revisi = 'revisi';
    case Persiapan = 'persiapan';
    case Diterima = 'diterima';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Pengajuan => 'Pengajuan',
            self::Pengujian => 'Pengujian',
            self::Revisi => 'Revisi',
            self::Persiapan => 'Persiapan',
            self::Diterima => 'Diterima',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Draft => 'Aplikasi masih dalam tahap draft.',
            self::Pengajuan => 'Aplikasi masih diajukan OPD.',
            self::Pengujian => 'Aplikasi diuji pihak IT Kominfo serta ranah pembahasan bersama.',
            self::Revisi => 'Perlu beberapa perbaikan oleh developer.',
            self::Persiapan => 'Aplikasi dalam tahap persiapan production.',
            self::Diterima => 'Aplikasi berhasil production.',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
