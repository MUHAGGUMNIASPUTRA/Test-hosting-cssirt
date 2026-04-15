<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class PostService
{
    public function __construct(private readonly AttachmentService $attachmentService) {}

    /**
     * Sync categories and tags for a post.
     */
    public function syncTaxonomy(Post $post, array $validated): void
    {
        $post->categories()->sync($validated['categories']);
        $post->tags()->sync($validated['tags'] ?? []);
    }

    /**
     * Create a new post and sync taxonomy.
     */
    public function create(array $validated, ?UploadedFile $image, string $authorName): Post
    {
        $attachment = $this->attachmentService->resolve(
            $image,
            $validated['image_type'] ?? null,
            $validated['image_url'] ?? null,
            null,
            'public',
            'posts',
        );

        $post = Post::create([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
            'body' => $validated['body'],
            'excerpt' => $validated['excerpt'],
            'image_id' => $attachment?->id,
            'status' => $validated['status'],
            'published_by' => $authorName,
            'published_at' => $validated['status'] === 'Published' ? now() : null,
        ]);

        $this->syncTaxonomy($post, $validated);

        return $post;
    }

    /**
     * Delete a post along with its image attachment and taxonomy relations.
     */
    public function deleteWithAssets(Post $post): void
    {
        $this->attachmentService->delete($post->image);

        $post->categories()->detach();
        $post->tags()->detach();
        $post->delete();
    }

    /**
     * Update an existing post and sync taxonomy.
     */
    public function update(Post $post, array $validated, ?UploadedFile $image, string $authorName): void
    {
        $post->loadMissing('image');

        $attachment = $this->attachmentService->resolve(
            $image,
            $validated['image_type'] ?? null,
            $validated['image_url'] ?? null,
            $post->image,
            'public',
            'posts',
        );

        $post->update([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
            'body' => $validated['body'],
            'excerpt' => $validated['excerpt'],
            'image_id' => $attachment?->id,
            'status' => $validated['status'],
            'published_by' => $authorName,
            'published_at' => $validated['status'] === 'Published'
                ? ($post->published_at ?? now())
                : null,
        ]);

        $this->syncTaxonomy($post, $validated);
    }
}
