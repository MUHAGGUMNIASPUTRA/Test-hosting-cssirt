<?php

namespace App\Models;

use App\Traits\HasUuidV6;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VirtualAssetGuideAttachment extends Model
{
    use HasUuidV6;

    protected $fillable = [
        'virtual_asset_guide_id',
        'document_id',
        'sort_order',
    ];

    public function guide(): BelongsTo
    {
        return $this->belongsTo(VirtualAssetGuide::class, 'virtual_asset_guide_id');
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }
}
