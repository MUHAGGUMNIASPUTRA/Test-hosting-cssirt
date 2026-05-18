<?php

// Tujuan: Model pegawai dengan enkripsi field sensitif (NIP, NIK, phone, email)
// Caller: EmployeeController, EmployeeService
// Side Effects: none

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
        'year_joined',
        'is_active',
    ];

    protected $casts = [
        'nip' => 'encrypted',
        'nik' => 'encrypted',
        'phone' => 'encrypted',
        'email' => 'encrypted',
        'is_active' => 'boolean',
        'year_joined' => 'integer',
    ];

    // Sensitive fields are never serialized directly — only masked versions are exposed
    protected $hidden = ['nip', 'nik', 'phone', 'email'];

    protected $appends = ['nip_masked', 'nik_masked', 'phone_masked', 'email_masked'];

    public function getNipMaskedAttribute(): ?string
    {
        return $this->maskNipNik($this->nip);
    }

    public function getNikMaskedAttribute(): ?string
    {
        return $this->maskNipNik($this->nik);
    }

    public function getPhoneMaskedAttribute(): ?string
    {
        return $this->maskPhone($this->phone);
    }

    public function getEmailMaskedAttribute(): ?string
    {
        return $this->maskEmail($this->email);
    }

    private function maskNipNik(?string $value): ?string
    {
        if (! $value) {
            return null;
        }
        // Already masked (contains *) — return as-is (handles legacy masked data)
        if (str_contains($value, '*')) {
            return $value;
        }
        $clean = preg_replace('/\D/', '', $value);
        $len = strlen($clean);
        if ($len <= 8) {
            return str_repeat('*', $len);
        }

        return substr($clean, 0, 5).str_repeat('*', $len - 8).substr($clean, -3);
    }

    private function maskPhone(?string $value): ?string
    {
        if (! $value) {
            return null;
        }
        $len = strlen($value);
        if ($len <= 8) {
            return str_repeat('*', $len);
        }

        return substr($value, 0, 4).str_repeat('*', max(4, $len - 8)).substr($value, -4);
    }

    private function maskEmail(?string $value): ?string
    {
        if (! $value) {
            return null;
        }
        $parts = explode('@', $value);
        if (count($parts) !== 2) {
            return str_repeat('*', strlen($value));
        }
        $local = $parts[0];

        return substr($local, 0, 1).str_repeat('*', max(3, strlen($local) - 1)).'@'.$parts[1];
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
