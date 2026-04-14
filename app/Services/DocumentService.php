<?php

namespace App\Services;

use App\Models\Document;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
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
     * Return a paginated, filtered, transformed collection of documents.
     *
     * @param  array{search?: string, areas?: array}  $filters
     */
    public function list(array $filters = []): LengthAwarePaginator
    {
        $query = Document::with('documentArea:id,name')->orderBy('title');

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'ilike', "%{$search}%")
                    ->orWhere('description', 'ilike', "%{$search}%")
                    ->orWhere('version', 'ilike', "%{$search}%");
            });
        }

        if (! empty($filters['areas'])) {
            $areas = (array) $filters['areas'];
            $includeNoArea = \in_array('0', $areas) || \in_array(0, $areas);
            $areaIds = array_values(array_filter($areas, fn ($a) => $a != '0' && $a != 0));

            $query->where(function ($q) use ($areaIds, $includeNoArea) {
                if ($areaIds) {
                    $q->whereIn('document_area_id', $areaIds);
                }
                if ($includeNoArea) {
                    $q->orWhereNull('document_area_id');
                }
            });
        }

        if (! empty($filters['stage'])) {
            $query->where('stage', $filters['stage']);
        }

        if (isset($filters['is_public']) && $filters['is_public'] !== '') {
            $query->where('is_public', (bool) $filters['is_public']);
        }

        $documents = $query->paginate(
            $filters['per_page'] ?? 10,
            ['*'],
            'page',
            $filters['page'] ?? 1
        );

        $documents->getCollection()->transform(function (Document $document) {
            $document->file_size = $document->fileSize();
            $document->file_exists = $document->fileExists();
            $document->pub_status = $this->getDocumentStatus($document);

            return $document;
        });

        return $documents;
    }

    /**
     * Create a new document record.
     */
    public function create(
        array $validated,
        ?UploadedFile $officialFile
    ): Document {
        $officialFilePath = $this->resolveOfficialFile($validated, $officialFile);

        return Document::create([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
            'description' => $validated['description'] ?? null,
            'draft_file_path' => $validated['doc_file_link'] ?? null,
            'official_file_path' => $officialFilePath,
            'reference_number' => $validated['reference_number'] ?? null,
            'stage' => $validated['stage'] ?? null,
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
        ?UploadedFile $officialFile
    ): void {
        $officialFilePath = $this->resolveOfficialFile($validated, $officialFile, $document);

        $document->update([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
            'description' => $validated['description'] ?? null,
            'draft_file_path' => $validated['doc_file_link'] ?? null,
            'official_file_path' => $officialFilePath,
            'reference_number' => $validated['reference_number'] ?? null,
            'stage' => $validated['stage'] ?? null,
            'version' => $validated['version'] ?? null,
            'published_at' => $validated['published_at'] ?? null,
            'is_public' => $validated['is_public'] ?? false,
            'document_area_id' => $validated['document_area_id'] ?? null,
        ]);
    }
}
