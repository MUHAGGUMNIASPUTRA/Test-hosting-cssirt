<?php

namespace Tests\Unit\Services;

use App\Models\Document;
use App\Services\DocumentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Unit tests for DocumentService.
 *
 * Tests for resolveOfficialFile and getDocumentStatus use Storage::fake or
 * unsaved model instances and do NOT require a database connection.
 * Tests for create/update use RefreshDatabase and need a PostgreSQL test DB.
 *
 * Run: php artisan test --filter DocumentServiceTest
 */
class DocumentServiceTest extends TestCase
{
    use RefreshDatabase;

    private DocumentService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DocumentService();
    }

    // -------------------------------------------------------------------------
    // resolveOfficialFile — Storage::fake, no DB
    // -------------------------------------------------------------------------

    public function test_resolve_official_file_returns_url_when_type_is_link(): void
    {
        Storage::fake('public');

        $validated = [
            'official_file_type' => 'link',
            'official_file_link' => 'https://jdih.example.go.id/dokumen.pdf',
        ];

        $result = $this->service->resolveOfficialFile($validated, null, null);

        $this->assertSame('https://jdih.example.go.id/dokumen.pdf', $result);
    }

    public function test_resolve_official_file_returns_null_when_type_is_link_but_link_is_empty(): void
    {
        Storage::fake('public');

        $validated = [
            'official_file_type' => 'link',
            'official_file_link' => '',
        ];

        $result = $this->service->resolveOfficialFile($validated, null, null);

        $this->assertNull($result);
    }

    public function test_resolve_official_file_keeps_existing_link_when_no_new_link(): void
    {
        Storage::fake('public');

        $existing             = new Document();
        $existing->official_file_path = 'https://old-link.example.com/file.pdf';

        $validated = [
            'official_file_type' => 'link',
            'official_file_link' => '',
        ];

        $result = $this->service->resolveOfficialFile($validated, null, $existing);

        $this->assertSame('https://old-link.example.com/file.pdf', $result);
    }

    public function test_resolve_official_file_stores_uploaded_file_when_type_is_file(): void
    {
        Storage::fake('public');

        $file      = UploadedFile::fake()->create('peraturan.pdf', 200, 'application/pdf');
        $validated = ['official_file_type' => 'file'];

        $result = $this->service->resolveOfficialFile($validated, $file, null);

        $this->assertNotNull($result);
        $this->assertStringStartsWith('documents/official/', $result);
        Storage::disk('public')->assertExists($result);
    }

    public function test_resolve_official_file_deletes_old_stored_file_on_new_upload(): void
    {
        Storage::fake('public');

        // Seed an existing stored file
        Storage::disk('public')->put('documents/official/old.pdf', 'old content');
        $existing                      = new Document();
        $existing->official_file_path  = 'documents/official/old.pdf';

        $file      = UploadedFile::fake()->create('new.pdf', 100, 'application/pdf');
        $validated = ['official_file_type' => 'file'];

        $result = $this->service->resolveOfficialFile($validated, $file, $existing);

        Storage::disk('public')->assertMissing('documents/official/old.pdf');
        Storage::disk('public')->assertExists($result);
    }

    public function test_resolve_official_file_does_not_delete_external_url_on_new_upload(): void
    {
        Storage::fake('public');

        $existing                     = new Document();
        $existing->official_file_path = 'https://external.example.com/file.pdf';

        $file      = UploadedFile::fake()->create('new.pdf', 100, 'application/pdf');
        $validated = ['official_file_type' => 'file'];

        // Should not throw; external URL cannot be deleted from disk
        $result = $this->service->resolveOfficialFile($validated, $file, $existing);

        $this->assertStringStartsWith('documents/official/', $result);
    }

    public function test_resolve_official_file_keeps_existing_when_no_new_upload(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('documents/official/existing.pdf', 'content');

        $existing                     = new Document();
        $existing->official_file_path = 'documents/official/existing.pdf';

        $validated = ['official_file_type' => 'file'];

        $result = $this->service->resolveOfficialFile($validated, null, $existing);

        $this->assertSame('documents/official/existing.pdf', $result);
    }

    // -------------------------------------------------------------------------
    // getDocumentStatus — pure logic, uses unsaved Document instances
    // -------------------------------------------------------------------------

    public function test_get_document_status_returns_draft_when_no_published_at(): void
    {
        $document               = new Document();
        $document->published_at = null;

        $this->assertSame('Draft', $this->service->getDocumentStatus($document));
    }

    public function test_get_document_status_returns_scheduled_when_published_at_is_in_future(): void
    {
        $document               = new Document();
        $document->published_at = now()->addDays(7);

        $this->assertSame('Scheduled', $this->service->getDocumentStatus($document));
    }

    public function test_get_document_status_returns_published_when_published_at_is_in_past(): void
    {
        $document               = new Document();
        $document->published_at = now()->subDay();

        $this->assertSame('Published', $this->service->getDocumentStatus($document));
    }

    // -------------------------------------------------------------------------
    // create — needs DB
    // -------------------------------------------------------------------------

    public function test_create_stores_document_with_correct_slug(): void
    {
        Storage::fake('public');

        $validated = [
            'title'              => 'Panduan Keamanan Siber',
            'description'        => 'Panduan umum',
            'official_file_type' => 'link',
            'official_file_link' => 'https://example.go.id/panduan.pdf',
            'is_public'          => true,
            'published_at'       => null,
        ];

        $document = $this->service->create($validated, null, null);

        $this->assertInstanceOf(Document::class, $document);
        $this->assertDatabaseHas('documents', [
            'title'              => 'Panduan Keamanan Siber',
            'slug'               => 'panduan-keamanan-siber',
            'official_file_path' => 'https://example.go.id/panduan.pdf',
        ]);
    }

    public function test_create_stores_uploaded_official_file(): void
    {
        Storage::fake('public');

        $file      = UploadedFile::fake()->create('peraturan.pdf', 300, 'application/pdf');
        $validated = [
            'title'              => 'Peraturan Daerah',
            'description'        => null,
            'official_file_type' => 'file',
            'is_public'          => false,
            'published_at'       => null,
        ];

        $document = $this->service->create($validated, null, $file);

        $path = $document->official_file_path;
        $this->assertNotNull($path);
        Storage::disk('public')->assertExists($path);
    }
}
