<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Document\StoreDocumentRequest;
use App\Http\Requests\Admin\Document\UpdateDocumentRequest;
use App\Models\Document;
use App\Models\DocumentArea;
use App\Services\DocumentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class DocumentController extends Controller
{
    public function __construct(private readonly DocumentService $documentService) {}

    public function index(): Response
    {
        return Inertia::render('Admin/Documents/Index', [
            'documentAreas' => DocumentArea::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Documents/Create', [
            'documentAreas' => DocumentArea::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(StoreDocumentRequest $request): RedirectResponse
    {
        $this->documentService->create(
            $request->validated(),
            $request->file('doc_file'),
            $request->file('official_file')
        );

        return redirect()->route('admin.documents.index')
            ->with('success', 'Dokumen berhasil ditambahkan.');
    }

    public function edit(Document $document): Response
    {
        $document->file_size = $document->fileSize();
        $document->file_exists = $document->fileExists();

        return Inertia::render('Admin/Documents/Create', [
            'document' => $document,
            'documentAreas' => DocumentArea::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(UpdateDocumentRequest $request, Document $document): RedirectResponse
    {
        $this->documentService->update(
            $document,
            $request->validated(),
            $request->file('doc_file'),
            $request->file('official_file')
        );

        return redirect()->route('admin.documents.index')
            ->with('success', 'Dokumen berhasil diperbarui.');
    }

    public function destroy(Document $document): RedirectResponse
    {
        if ($document->official_file_path && ! str_starts_with($document->official_file_path, 'http')) {
            Storage::disk('public')->delete($document->official_file_path);
        }

        $document->delete();

        return redirect()->route('admin.documents.index')
            ->with('success', 'Dokumen berhasil dihapus.');
    }

    public function toggleVisibility(Document $document): RedirectResponse
    {
        $document->update(['is_public' => ! $document->is_public]);

        $status = $document->is_public ? 'dipublikasikan' : 'disembunyikan dari publik';

        return back()->with('success', "Dokumen berhasil {$status}.");
    }
}
