<?php

namespace App\Models;

use App\Traits\HasUuidV6;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TechStack extends Model
{
    use HasUuidV6;

    protected $fillable = [
        'name',
        'description',
        'category_id',
        'logo_attachment_id',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(TechStackCategory::class, 'category_id');
    }

    public function logoAttachment(): BelongsTo
    {
        return $this->belongsTo(Attachment::class, 'logo_attachment_id');
    }
}
