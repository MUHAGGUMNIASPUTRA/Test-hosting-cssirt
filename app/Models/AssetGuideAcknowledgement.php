<?php

namespace App\Models;

use App\Traits\HasUuidV6;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AssetGuideAcknowledgement extends Model
{
    use HasUuidV6;

    protected $fillable = [
        'asset_type',
        'asset_id',
        'guide_id',
        'acknowledged_by',
        'acknowledged_at',
    ];

    protected $casts = [
        'acknowledged_at' => 'datetime',
    ];

    public function asset(): MorphTo
    {
        return $this->morphTo();
    }

    public function guide(): BelongsTo
    {
        return $this->belongsTo(VirtualAssetGuide::class);
    }

    public function acknowledgedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'acknowledged_by');
    }
}
