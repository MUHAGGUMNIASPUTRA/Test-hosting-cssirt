<?php

namespace App\Models;

use App\Enums\VirtualGuideType;
use App\Traits\HasUuidV6;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VirtualAssetGuide extends Model
{
    use HasUuidV6;

    protected $fillable = [
        'name',
        'description',
        'type',
    ];

    protected $casts = [
        'type' => VirtualGuideType::class,
    ];

    public function guideAttachments(): HasMany
    {
        return $this->hasMany(VirtualAssetGuideAttachment::class)->orderBy('sort_order');
    }
}
