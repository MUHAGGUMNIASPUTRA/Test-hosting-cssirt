<?php

namespace App\Models;

use App\Enums\OwnerContactType;
use App\Traits\HasUuidV6;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class License extends Model
{
    use HasUuidV6;

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'version',
        'expired_at',
        'location_id',
        'provider_org_id',
        'owner_org_id',
        'owner_contact_type',
        'owner_employee_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'expired_at' => 'date',
        'owner_contact_type' => OwnerContactType::class,
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function providerOrg(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'provider_org_id');
    }

    public function ownerOrg(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'owner_org_id');
    }

    public function ownerEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'owner_employee_id');
    }

    public function securityClassification(): MorphOne
    {
        return $this->morphOne(AssetSecurityClassification::class, 'asset');
    }

    public function auditLogs(): MorphMany
    {
        return $this->morphMany(AssetAuditLog::class, 'asset')->latest();
    }
}
