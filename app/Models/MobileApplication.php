<?php

// Tujuan: Model aplikasi mobile beserta relasi aset dan insiden terdampak
// Caller: MobileApplicationController, MobileApplicationService
// Side Effects: none

namespace App\Models;

use App\Enums\AppStatus;
use App\Enums\AssetStage;
use App\Enums\OwnerContactType;
use App\Traits\HasUuidV6;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class MobileApplication extends Model
{
    use HasUuidV6;

    protected $fillable = [
        'name',
        'description',
        'stage',
        'app_status',
        'current_version',
        'app_link',
        'location_id',
        'provider_org_id',
        'owner_org_id',
        'owner_contact_type',
        'owner_employee_id',
        'vendor_id',
    ];

    protected $casts = [
        'stage' => AssetStage::class,
        'app_status' => AppStatus::class,
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

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function techStacks(): HasMany
    {
        return $this->hasMany(MobileAppTechStack::class);
    }

    public function incidents(): MorphToMany
    {
        return $this->morphToMany(Incident::class, 'assetable', 'incident_virtual_assets', 'assetable_id', 'incident_id');
    }

    public function securityClassification(): MorphOne
    {
        return $this->morphOne(AssetSecurityClassification::class, 'asset');
    }

    public function auditLogs(): MorphMany
    {
        return $this->morphMany(AssetAuditLog::class, 'asset')->latest();
    }

    public function securityNotes(): MorphMany
    {
        return $this->morphMany(AssetSecurityNote::class, 'asset')->latest();
    }

    public function guideAcknowledgements(): MorphMany
    {
        return $this->morphMany(AssetGuideAcknowledgement::class, 'asset');
    }
}
