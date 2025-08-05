<?php
// File: app/Models/Incident.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

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
    'reporter_name',
    'reporter_email',
    'reporter_phone',
    'incident_type_id',
    'description',
    'attachment',
    'incident_at',
    'status',
    'priority',
    'assigned_to',
    'reported_at',
    'resolved_at',
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
  ];

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
   * * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
  public function incidentLogs(): HasMany
  {
    // An incident has many logs. Order by the latest first.
    return $this->hasMany(IncidentLog::class)->latest();
  }

  /**
   * Get the file size in human readable format
   */
  public function fileSize()
  {
    if ($this->attachment !== null && Storage::disk('public')->exists($this->attachment)) {
      $bytes = Storage::disk('public')->size($this->attachment);
      return $this->formatBytes($bytes);
    }
    return 'N/A';
  }

  /**
   * Format bytes to human readable format
   */
  private function formatBytes($bytes, $precision = 2)
  {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    for ($i = 0; $bytes > 1024; $i++) {
      $bytes /= 1024;
    }
    return round($bytes, $precision) . ' ' . $units[$i];
  }
}
