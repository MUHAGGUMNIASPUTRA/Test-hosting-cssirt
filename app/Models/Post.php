<?php
// app/Models/Post.php

namespace App\Models;

use App\Enums\PostStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string      $title
 * @property string      $slug
 * @property string|null $image
 * @property PostStatus  $status
 * @property string      $excerpt
 * @property string      $body
 * @property string|null $published_by
 */
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
    'ratings_count',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array
   */
  protected $casts = [
    'published_at' => 'datetime',
    'status'       => PostStatus::class,
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

  /**
   * Get the ratings for the blog post.
   * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
  public function ratings(): HasMany
  {
    return $this->hasMany(Rating::class);
  }
}
