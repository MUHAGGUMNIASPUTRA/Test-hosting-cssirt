<?php
// app/Models/Post.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Post extends Model
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
    'image',
    'status',
    'excerpt',
    'body',
    'views_count',
    'published_at',
    'published_by',
    'rating',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array
   */
  protected $casts = [
    'published_at' => 'datetime',
  ];

  /**
   * The categories that belong to the Post.
   */
  public function categories(): BelongsToMany
  {
    return $this->belongsToMany(Category::class);
  }

  /**
   * The tags that belong to the Post.
   */
  public function tags(): BelongsToMany
  {
    return $this->belongsToMany(Tag::class);
  }
}
