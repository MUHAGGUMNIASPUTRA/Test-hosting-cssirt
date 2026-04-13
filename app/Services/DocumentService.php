<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentService
{
    /**
     * Resolve which path to store for official_file_path.
     * Returns a storage path (relative) for uploaded PDFs,
     * or a URL string for external links.
     */
    public function resolveOfficialFile(
        array $validated,
        ?UploadedFile $file,
        ?Document $existing = null
    ): ?string {
        if ($validated['official_file_type'] === 'file') {
            if ($file !== null) {
                // Delete old stored file if it's not a URL
                $old = $existing?->official_file_path;
                if ($old && ! str_starts_with($old, 'http')) {
                    Storage::disk('public')->delete($old);
                }

                return $file->store('documents/official', 'public');
            }

            // No new upload — keep existing
            return $existing?->official_file_path;
        }

        // mode link
        return ! empty($validated['official_file_link'])
            ? $validated['official_file_link']
            : $existing?->official_file_path;
    }

    /**
     * Get the publication status label for a document.
     */
    public function getDocumentStatus(Document $document): string
    {
        if (! $document->published_at) {
            return 'Draft';
        }

        if ($document->published_at > now()) {
            return 'Scheduled';
        }

        return 'Published';
    }

    /**
     * Create a new document record.
     */
    public function create(
        array $validated,
        ?UploadedFile $docFile,
        ?UploadedFile $officialFile
    ): Document {
        $officialFilePath = $this->resolveOfficialFile($validated, $officialFile);

        return Document::create([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
            'description' => $validated['description'] ?? null,
            'file_path' => $validated['doc_file_link'] ?? null,
            'official_file_path' => $officialFilePath,
            'version' => $validated['version'] ?? null,
            'published_at' => $validated['published_at'] ?? null,
            'is_public' => $validated['is_public'] ?? false,
            'document_area_id' => $validated['document_area_id'] ?? null,
        ]);
    }

    /**
     * Update an existing document record.
     */
    public function update(
        Document $document,
        array $validated,
        ?UploadedFile $docFile,
        ?UploadedFile $officialFile
    ): void {
        $officialFilePath = $this->resolveOfficialFile($validated, $officialFile, $document);

        $document->update([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
            'description' => $validated['description'] ?? null,
            'file_path' => $validated['doc_file_link'] ?? null,
            'official_file_path' => $officialFilePath,
            'version' => $validated['version'] ?? null,
            'published_at' => $validated['published_at'] ?? null,
            'is_public' => $validated['is_public'] ?? false,
            'document_area_id' => $validated['document_area_id'] ?? null,
        ]);
    }
}
