<?php

namespace App\Models;

use App\Traits\HasUuidV6;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AssetSecurityClassification extends Model
{
    use HasUuidV6;

    protected $fillable = [
        'asset_type',
        'asset_id',
        'confidentiality',
        'integrity',
        'availability',
        'notes',
    ];

    protected $casts = [
        'confidentiality' => 'integer',
        'integrity' => 'integer',
        'availability' => 'integer',
    ];

    public function asset(): MorphTo
    {
        return $this->morphTo();
    }

    public function getTotalAttribute(): int
    {
        return $this->confidentiality + $this->integrity + $this->availability;
    }
}
