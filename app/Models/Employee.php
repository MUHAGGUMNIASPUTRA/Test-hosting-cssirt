<?php

namespace App\Models;

use App\Traits\HasUuidV6;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends Model
{
    use HasUuidV6;

    protected $fillable = [
        'name',
        'nip',
        'nik',
        'phone',
        'email',
        'position_id',
        'organization_id',
        'year_joined',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'year_joined' => 'integer',
    ];

    public function setNipAttribute(?string $value): void
    {
        $this->attributes['nip'] = $value ? $this->maskIdentifier($value) : null;
    }

    public function setNikAttribute(?string $value): void
    {
        $this->attributes['nik'] = $value ? $this->maskIdentifier($value) : null;
    }

    private function maskIdentifier(string $value): string
    {
        $clean = preg_replace('/\D/', '', $value);
        $len = strlen($clean);
        if ($len <= 8) {
            return $clean;
        }

        $first = substr($clean, 0, 5);
        $last = substr($clean, -3);
        $middle = str_repeat('*', $len - 8);

        return $first.$middle.$last;
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
