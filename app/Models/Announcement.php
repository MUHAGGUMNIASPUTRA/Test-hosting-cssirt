<?php

// File: app/Models/Announcement.php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'content',
        'level',
        'start_date',
        'end_date',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the level options
     */
    public static function getLevelOptions()
    {
        return [
            'info' => 'Info',
            'warning' => 'Peringatan',
            'critical' => 'Kritis',
        ];
    }

    /**
     * Check if announcement is currently active
     */
    public function isCurrentlyActive()
    {
        $now = Carbon::now();

        return $this->is_active &&
               $this->start_date <= $now &&
               $this->end_date >= $now;
    }

    /**
     * Scope for active announcements
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for current announcements
     */
    public function scopeCurrent($query)
    {
        $now = Carbon::now();

        return $query->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now);
    }
}
