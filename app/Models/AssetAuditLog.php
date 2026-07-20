<?php

namespace App\Models;

use App\Enums\AuditDangerLevel;
use App\Traits\HasUuidV6;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property Attachment|null $attachment
 */
class AssetAuditLog extends Model
{
    use HasUuidV6;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'asset_type',
        'asset_id',
        'user_id',
        'message',
        'danger_level',
        'attachment_id',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'danger_level' => AuditDangerLevel::class,
    ];

    public function asset(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attachment(): BelongsTo
    {
        return $this->belongsTo(Attachment::class);
    }
}
