<?php

// File: app/Models/IncidentLog.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncidentLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
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
