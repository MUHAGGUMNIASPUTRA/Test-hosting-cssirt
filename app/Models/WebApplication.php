<?php

// Tujuan: Model aplikasi web beserta relasi aset dan insiden terdampak
// Caller: WebApplicationController, WebApplicationService
// Side Effects: none

namespace App\Models;

use App\Enums\AppStatus;
use App\Enums\AssetStage;
use App\Enums\HttpsStatus;
use App\Enums\OwnerContactType;
use App\Traits\HasUuidV6;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class WebApplication extends Model
{
    use HasUuidV6;

    protected $fillable = [
        'name',
        'description',
        'stage',
        'app_status',
        'https_status',
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
        'https_status' => HttpsStatus::class,
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

    public function vms(): HasMany
    {
        return $this->hasMany(WebAppVm::class)->orderBy('sort_order');
    }

    public function networks(): HasMany
    {
        return $this->hasMany(WebAppNetwork::class)->orderBy('sort_order');
    }

    public function primaryNetwork(): HasOne
    {
        return $this->hasOne(WebAppNetwork::class)->where('is_primary', true);
    }

    public function techStacks(): HasMany
    {
        return $this->hasMany(WebAppTechStack::class);
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
