<?php

namespace App\Enums;

enum PostStatus: string
{
    case Draft     = 'Draft';
    case Published = 'Published';

    /** @return string[] */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return $this->value;
    }

    public function isPublished(): bool
    {
        return $this === self::Published;
    }
}
