<?php

// Tujuan: Model aset fisik (hardware, perangkat, dll)
// Caller: PhysicalAssetController, PhysicalAssetService
// Side Effects: none

namespace App\Models;

use App\Enums\OwnerContactType;
use App\Traits\HasUuidV6;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class PhysicalAsset extends Model
{
    use HasUuidV6;

    protected $fillable = [
        'asset_code',
        'name',
        'description',
        'specifications',
        'year',
        'attachment_id',
        'location_id',
        'owner_org_id',
        'owner_contact_type',
        'owner_employee_id',
    ];

    protected $casts = [
        'year' => 'integer',
        'owner_contact_type' => OwnerContactType::class,
    ];

    public function attachment(): BelongsTo
    {
        return $this->belongsTo(Attachment::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
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

    public function securityNotes(): MorphMany
    {
        return $this->morphMany(AssetSecurityNote::class, 'asset');
    }
}
