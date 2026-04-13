<?php

namespace Tests\Unit\Services;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Services\PostService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Unit tests for PostService.
 *
 * Tests for resolveImage use Storage::fake and do NOT require a database.
 * Tests for syncTaxonomy, create, and deleteWithAssets use RefreshDatabase
 * and require a PostgreSQL test database.
 *
 * Run: php artisan test --filter PostServiceTest
 */
class PostServiceTest extends TestCase
{
    use RefreshDatabase;

    private PostService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PostService;
    }

    // -------------------------------------------------------------------------
    // resolveImage — Storage::fake, no DB
    // -------------------------------------------------------------------------

    public function test_resolve_image_keeps_existing_when_no_new_upload_and_type_is_file(): void
    {
        $validated = ['image_type' => 'file'];

        $result = $this->service->resolveImage($validated, null, 'posts/existing.jpg');

        $this->assertSame('posts/existing.jpg', $result);
    }

    public function test_resolve_image_stores_new_file_and_deletes_old_stored_file(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('posts/old.jpg', 'old image content');

        $file = UploadedFile::fake()->image('new.jpg');
        $validated = ['image_type' => 'file'];

        $result = $this->service->resolveImage($validated, $file, 'posts/old.jpg');

        Storage::disk('public')->assertMissing('posts/old.jpg');
        Storage::disk('public')->assertExists($result);
        $this->assertStringStartsWith('posts/', $result);
    }

    public function test_resolve_image_does_not_delete_old_external_url_on_new_upload(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('new.jpg');
        $validated = ['image_type' => 'file'];

        // External URL should not be passed to Storage::delete
        $result = $this->service->resolveImage($validated, $file, 'https://external.example.com/old.jpg');

        $this->assertStringStartsWith('posts/', $result);
        Storage::disk('public')->assertExists($result);
    }

    public function test_resolve_image_returns_url_when_type_is_link(): void
    {
        $validated = [
            'image_type' => 'link',
            'image_url' => 'https://cdn.example.com/image.jpg',
        ];

        $result = $this->service->resolveImage($validated, null, null);

        $this->assertSame('https://cdn.example.com/image.jpg', $result);
    }

    public function test_resolve_image_returns_null_when_type_is_link_but_url_is_empty(): void
    {
        $validated = [
            'image_type' => 'link',
            'image_url' => '',
        ];

        $result = $this->service->resolveImage($validated, null, 'posts/existing.jpg');

        $this->assertNull($result);
    }

    // -------------------------------------------------------------------------
    // syncTaxonomy — needs DB
    // -------------------------------------------------------------------------

    public function test_sync_taxonomy_attaches_categories_and_tags_to_post(): void
    {
        $post = Post::create([
            'title' => 'Test Post',
            'slug' => 'test-post',
            'excerpt' => 'Excerpt',
            'body' => 'Body content',
            'status' => 'Draft',
        ]);

        $cat1 = Category::create(['name' => 'Keamanan', 'slug' => 'keamanan']);
        $cat2 = Category::create(['name' => 'Edukasi', 'slug' => 'edukasi']);
        $tag1 = Tag::create(['name' => 'PHP', 'slug' => 'php']);
        $tag2 = Tag::create(['name' => 'Laravel', 'slug' => 'laravel']);

        $this->service->syncTaxonomy($post, [
            'categories' => [$cat1->id, $cat2->id],
            'tags' => [$tag1->id, $tag2->id],
        ]);

        $this->assertCount(2, $post->fresh()->categories);
        $this->assertCount(2, $post->fresh()->tags);
        $this->assertTrue($post->fresh()->categories->contains($cat1));
        $this->assertTrue($post->fresh()->tags->contains($tag2));
    }

    public function test_sync_taxonomy_removes_old_categories_on_re_sync(): void
    {
        $post = Post::create([
            'title' => 'Re-sync Post',
            'slug' => 're-sync-post',
            'excerpt' => 'Excerpt',
            'body' => 'Body',
            'status' => 'Draft',
        ]);

        $cat1 = Category::create(['name' => 'Cat A', 'slug' => 'cat-a']);
        $cat2 = Category::create(['name' => 'Cat B', 'slug' => 'cat-b']);

        // First sync with cat1
        $this->service->syncTaxonomy($post, ['categories' => [$cat1->id], 'tags' => []]);
        $this->assertCount(1, $post->fresh()->categories);

        // Re-sync with cat2 only — cat1 should be removed
        $this->service->syncTaxonomy($post, ['categories' => [$cat2->id], 'tags' => []]);
        $this->assertCount(1, $post->fresh()->categories);
        $this->assertFalse($post->fresh()->categories->contains($cat1));
        $this->assertTrue($post->fresh()->categories->contains($cat2));
    }

    public function test_sync_taxonomy_accepts_empty_tags(): void
    {
        $post = Post::create([
            'title' => 'No Tags Post',
            'slug' => 'no-tags-post',
            'excerpt' => 'Excerpt',
            'body' => 'Body',
            'status' => 'Draft',
        ]);

        $cat = Category::create(['name' => 'Solo', 'slug' => 'solo']);

        // tags key omitted / empty — should not throw
        $this->service->syncTaxonomy($post, ['categories' => [$cat->id]]);

        $this->assertCount(1, $post->fresh()->categories);
        $this->assertCount(0, $post->fresh()->tags);
    }

    // -------------------------------------------------------------------------
    // deleteWithAssets — needs DB + Storage::fake
    // -------------------------------------------------------------------------

    public function test_delete_with_assets_removes_stored_image_and_post(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('posts/cover.jpg', 'image data');

        $post = Post::create([
            'title' => 'Delete Me',
            'slug' => 'delete-me',
            'excerpt' => 'Excerpt',
            'body' => 'Body',
            'status' => 'Draft',
            'image' => 'posts/cover.jpg',
        ]);

        $this->service->deleteWithAssets($post);

        Storage::disk('public')->assertMissing('posts/cover.jpg');
        $this->assertDatabaseMissing('posts', ['slug' => 'delete-me']);
    }

    public function test_delete_with_assets_does_not_delete_external_image_url(): void
    {
        Storage::fake('public');

        $post = Post::create([
            'title' => 'External Image Post',
            'slug' => 'external-image-post',
            'excerpt' => 'Excerpt',
            'body' => 'Body',
            'status' => 'Draft',
            'image' => 'https://external.example.com/cover.jpg',
        ]);

        // Should not throw even though we can't delete the external URL
        $this->service->deleteWithAssets($post);

        $this->assertDatabaseMissing('posts', ['slug' => 'external-image-post']);
    }
}
