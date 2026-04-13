<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostService
{
    /**
     * Resolve which image path to store.
     * Returns a storage path (relative) for uploads, a URL for external links,
     * or the existing path if no new image is provided.
     */
    public function resolveImage(
        array $validated,
        ?UploadedFile $image,
        ?string $existing = null
    ): ?string {
        if (($validated['image_type'] ?? 'file') === 'file') {
            if ($image !== null) {
                // Delete old stored image if it's not a URL
                if ($existing && !str_starts_with($existing, 'http')) {
                    Storage::disk('public')->delete($existing);
                }
                return $image->store('posts', 'public');
            }
            return $existing;
        }

        // mode link
        if (($validated['image_type'] ?? null) === 'link') {
            return !empty($validated['image_url']) ? $validated['image_url'] : null;
        }

        return $existing;
    }

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
        $path = $this->resolveImage($validated, $image);

        $post = Post::create([
            'title'        => $validated['title'],
            'slug'         => Str::slug($validated['title']),
            'body'         => $validated['body'],
            'excerpt'      => $validated['excerpt'],
            'image'        => $path,
            'status'       => $validated['status'],
            'published_by' => $authorName,
            'published_at' => $validated['status'] === 'Published' ? now() : null,
        ]);

        $this->syncTaxonomy($post, $validated);

        return $post;
    }

    /**
     * Delete a post along with its stored image and taxonomy relations.
     */
    public function deleteWithAssets(Post $post): void
    {
        if ($post->image && !str_starts_with($post->image, 'http')) {
            Storage::disk('public')->delete($post->image);
        }

        $post->categories()->detach();
        $post->tags()->detach();
        $post->delete();
    }

    /**
     * Update an existing post and sync taxonomy.
     */
    public function update(Post $post, array $validated, ?UploadedFile $image, string $authorName): void
    {
        $path = $this->resolveImage($validated, $image, $post->image);

        $post->update([
            'title'        => $validated['title'],
            'slug'         => Str::slug($validated['title']),
            'body'         => $validated['body'],
            'excerpt'      => $validated['excerpt'],
            'image'        => $path,
            'status'       => $validated['status'],
            'published_by' => $authorName,
            'published_at' => $validated['status'] === 'Published'
                ? ($post->published_at ?? now())
                : null,
        ]);

        $this->syncTaxonomy($post, $validated);
    }
}
