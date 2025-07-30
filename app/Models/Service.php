<?php
// File: app/Models/Service.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Service extends Model
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
    'icon',
    'short_description',
    'full_description',
    'is_active',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array
   */
  protected $casts = [
    'is_active' => 'boolean',
  ];

  /**
   * Automatically generate slug from name on creating and updating.
   *
   * @return void
   */
  protected static function boot()
  {
    parent::boot();

    static::creating(function ($service) {
      if (empty($service->slug)) {
        $service->slug = Str::slug($service->name);
      }
    });

    static::updating(function ($service) {
      if ($service->isDirty('name') && empty($service->getOriginal('slug'))) {
        $service->slug = Str::slug($service->name);
      }
    });
  }
}
