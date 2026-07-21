<?php

namespace App\Http\Controllers\Admin;

use App\Enums\DocumentStage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Document\StoreDocumentRequest;
use App\Http\Requests\Admin\Document\UpdateDocumentRequest;
use App\Http\Resources\AttachmentResource;
use App\Models\Document;
use App\Models\DocumentArea;
use App\Services\AttachmentService;
use App\Services\DocumentService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class DocumentController extends Controller
{
    public function __construct(
        private readonly DocumentService $documentService,
        private readonly AttachmentService $attachmentService,
    ) {}

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
            'stageOptions' => DocumentStage::values(),
        ]);
    }

    public function store(StoreDocumentRequest $request): RedirectResponse
    {
        $this->documentService->create(
            $request->validated(),
            $request->file('official_file')
        );

        return redirect()->route('admin.documents.index')
            ->with('success', 'Dokumen berhasil ditambahkan.');
    }

    public function show(Document $document): Response
    {
        $document->load(['officialAttachment', 'documentArea']);
        $data = $document->toArray();
        $data['official_attachment'] = $document->officialAttachment
            ? (new AttachmentResource($document->officialAttachment))->toArray(request())
            : null;

        return Inertia::render('Admin/Documents/Show', ['document' => $data]);
    }

    public function edit(Document $document): Response
    {
        $document->load('officialAttachment');
        $data = $document->toArray();
        $data['official_attachment'] = $document->officialAttachment
            ? (new AttachmentResource($document->officialAttachment))->toArray(request())
            : null;

        return Inertia::render('Admin/Documents/Create', [
            'document' => $data,
            'documentAreas' => DocumentArea::orderBy('name')->get(['id', 'name']),
            'stageOptions' => DocumentStage::values(),
        ]);
    }

    public function update(UpdateDocumentRequest $request, Document $document): RedirectResponse
    {
        $this->documentService->update(
            $document,
            $request->validated(),
            $request->file('official_file')
        );

        return redirect()->route('admin.documents.index')
            ->with('success', 'Dokumen berhasil diperbarui.');
    }

    public function destroy(Document $document): RedirectResponse
    {
        $document->loadMissing('officialAttachment');
        $this->attachmentService->delete($document->officialAttachment);
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
