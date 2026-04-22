<?php

// Tujuan: Model insiden keamanan, termasuk relasi ke aset virtual terdampak
// Caller: IncidentController, IncidentService
// Side Effects: none

namespace App\Models;

use App\Enums\IncidentPriority;
use App\Enums\IncidentStatus;
use App\Traits\HasUuidV6;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\DB;

/**
 * @property IncidentStatus $status
 * @property IncidentPriority $priority
 * @property \App\Models\Attachment|null $attachment
 */
class Incident extends Model
{
    use HasFactory, HasUuidV6;

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

    protected $casts = [
        'incident_at' => 'datetime',
        'reported_at' => 'datetime',
        'resolved_at' => 'datetime',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'status' => IncidentStatus::class,
        'priority' => IncidentPriority::class,
    ];

    public function attachment(): BelongsTo
    {
        return $this->belongsTo(Attachment::class);
    }

    public function incidentType(): BelongsTo
    {
        return $this->belongsTo(IncidentType::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function incidentLogs(): HasMany
    {
        return $this->hasMany(IncidentLog::class)->orderBy('created_at', 'desc');
    }

    public function webApplications(): MorphToMany
    {
        return $this->morphedByMany(WebApplication::class, 'assetable', 'incident_virtual_assets', 'incident_id', 'assetable_id');
    }

    public function mobileApplications(): MorphToMany
    {
        return $this->morphedByMany(MobileApplication::class, 'assetable', 'incident_virtual_assets', 'incident_id', 'assetable_id');
    }

    public static function generateCaseId(): string
    {
        $period = now()->format('Y-m');
        $year = now()->format('Y');
        $month = now()->format('m');

        for ($attempt = 0; $attempt < 5; $attempt++) {
            try {
                return DB::transaction(function () use ($period, $year, $month) {
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
                usleep(50000);
            }
        }

        return 'CSIRT-'.$year.'-'.$month.'-'.str_pad((string) random_int(1, 999), 3, '0', STR_PAD_LEFT);
    }

    public function readBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'read_by');
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }
}
