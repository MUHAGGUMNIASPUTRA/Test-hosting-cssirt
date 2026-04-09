<?php
// File: app/Models/IncidentType.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IncidentType extends Model
{
  use HasFactory;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
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
