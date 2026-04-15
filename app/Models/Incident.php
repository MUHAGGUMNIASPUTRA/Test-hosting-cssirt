<?php

// filepath: app/Models/Incident.php

namespace App\Models;

use App\Enums\IncidentPriority;
use App\Enums\IncidentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

/**
 * @property IncidentStatus $status
 * @property IncidentPriority $priority
 * @property \App\Models\Attachment|null $attachment
 */
class Incident extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'case_id',
        'access_token',
        'reporter_name',
        'reporter_email',
        'reporter_phone',
        'incident_type_id',
        'description',
        'attachment_id',
        'incident_at',
        'status',
        'priority',
        'assigned_to',
        'reported_at',
        'resolved_at',
        'is_read',
        'read_by',
        'read_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'incident_at' => 'datetime',
        'reported_at' => 'datetime',
        'resolved_at' => 'datetime',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'status' => IncidentStatus::class,
        'priority' => IncidentPriority::class,
    ];

    /**
     * Get the attachment for the incident.
     */
    public function attachment(): BelongsTo
    {
        return $this->belongsTo(Attachment::class);
    }

    /**
     * Get the type of the incident.
     */
    public function incidentType(): BelongsTo
    {
        return $this->belongsTo(IncidentType::class);
    }

    /**
     * Get the user assigned to the incident.
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the logs for the incident.
     */
    public function incidentLogs(): HasMany
    {
        // An incident has many logs. Order by oldest first for timeline.
        return $this->hasMany(IncidentLog::class)->orderBy('created_at', 'desc');
    }

    /**
     * Generate a unique case ID with pattern CSIRT-YYYY-MM-XXX (monthly reset).
     * Uses incident_sequences(period) table to avoid race conditions.
     */
    public static function generateCaseId(): string
    {
        $period = now()->format('Y-m');
        $year = now()->format('Y');
        $month = now()->format('m');

        // Retry a few times on serialization conflicts
        for ($attempt = 0; $attempt < 5; $attempt++) {
            try {
                return DB::transaction(function () use ($period, $year, $month) {
                    // Lock the row for this period
                    $row = DB::table('incident_sequences')->where('period', $period)->lockForUpdate()->first();
                    if (! $row) {
                        DB::table('incident_sequences')->insert([
                            'period' => $period,
                            'last_number' => 0,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        $row = DB::table('incident_sequences')->where('period', $period)->lockForUpdate()->first();
                    }
                    $next = ($row->last_number ?? 0) + 1;
                    DB::table('incident_sequences')->where('period', $period)->update([
                        'last_number' => $next,
                        'updated_at' => now(),
                    ]);

                    $seq = str_pad((string) $next, 3, '0', STR_PAD_LEFT);

                    return "CSIRT-{$year}-{$month}-{$seq}";
                }, 3);
            } catch (\Throwable $e) {
                // backoff then retry
                usleep(50000); // 50ms
            }
        }

        // Fallback: include a random suffix to guarantee uniqueness
        return 'CSIRT-'.$year.'-'.$month.'-'.str_pad((string) random_int(1, 999), 3, '0', STR_PAD_LEFT);
    }

    /**
     * Get the user who read the incident.
     */
    public function readBy()
    {
        return $this->belongsTo(User::class, 'read_by');
    }

    /**
     * Scope a query to only include unread incidents.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope a query to only include read incidents.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }
}
