<?php

// Tujuan: Model aset informasi (dokumen, format penyimpanan, klasifikasi keamanan)
// Caller: InformationAssetController, InformationAssetService
// Side Effects: none

namespace App\Models;

use App\Traits\HasUuidV6;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class InformationAsset extends Model
{
    use HasUuidV6;

    protected $fillable = [
        'document_id',
        'storage_format',
        'location_id',
        'owner_org_id',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function ownerOrg(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'owner_org_id');
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
}
