<?php
// File: app/Models/Document.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
  use HasFactory;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'title',
    'slug',
    'description',
    'file_path',
    'official_file_path',
    'version',
    'published_at',
    'is_public',
    'document_area_id',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array
   */
  protected $casts = [
    'published_at' => 'datetime',
    'is_public' => 'boolean',
  ];

  public function documentArea()
  {
    return $this->belongsTo(DocumentArea::class);
  }

  /**
   * Get only published documents
   */
  public function scopePublished($query)
  {
    return $query->whereNotNull('published_at');
  }

  /**
   * Get the file size in human readable format
   */
  public function fileSize()
  {
    if ($this->file_path && Storage::disk('public')->exists($this->file_path)) {
      $bytes = Storage::disk('public')->size($this->file_path);
      return $this->formatBytes($bytes);
    }
    return 'N/A';
  }

  /**
   * Get the download URL
   */
  public function downloadUrl()
  {
    return Storage::disk('public')->path($this->file_path);
  }

  /**
   * Check if file exists
   */
  public function fileExists()
  {
    return Storage::disk('public')->exists($this->file_path);
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
