<?php

// File: app/Models/IncidentLog.php

namespace App\Models;

use App\Traits\HasUuidV6;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property Attachment|null $attachment
 */
class IncidentLog extends Model
{
    use HasFactory, HasUuidV6;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'incident_id',
        'user_id',
        'log_message',
        'is_public',
        'attachment_id',
    ];

    /**
     * Get the incident that the log belongs to.
     */
    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class);
    }

    /**
     * Get the user who created the log.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the attachment for this log entry.
     */
    public function attachment(): BelongsTo
    {
        return $this->belongsTo(Attachment::class);
    }
}
