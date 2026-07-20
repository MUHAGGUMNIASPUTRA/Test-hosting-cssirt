<?php

// File: app/Models/IncidentType.php

namespace App\Models;

use App\Traits\HasUuidV6;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IncidentType extends Model
{
    use HasFactory, HasUuidV6;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'guide',
    ];

    /**
     * Get the incidents for the incident type.
     */
    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class);
    }
}
