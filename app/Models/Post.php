<?php

// app/Models/Post.php

namespace App\Models;

use App\Enums\PostStatus;
use App\Traits\HasUuidV6;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property \App\Models\Attachment|null $image
 * @property PostStatus $status
 * @property string $title
 * @property string $slug
 * @property string $excerpt
 * @property string $body
 * @property string|null $published_by
 */
class Post extends Model
{
    use HasFactory, HasUuidV6;

    protected $fillable = [
        'title',
        'slug',
        'image_id',
        'status',
        'excerpt',
        'body',
        'views_count',
        'published_at',
        'published_by',
        'rating',
        'ratings_count',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'status' => PostStatus::class,
    ];

    /**
     * Get the featured image attachment for the post.
     */
    public function image(): BelongsTo
    {
        return $this->belongsTo(Attachment::class, 'image_id');
    }

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
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }
}
