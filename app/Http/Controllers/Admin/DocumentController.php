<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Document\StoreDocumentRequest;
use App\Http\Requests\Admin\Document\UpdateDocumentRequest;
use App\Models\Document;
use App\Models\DocumentArea;
use App\Services\DocumentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class DocumentController extends Controller
{
    public function __construct(private readonly DocumentService $documentService) {}

    public function index(Request $request): Response
    {
        $query = Document::with('documentArea:id,name')->orderBy('title');

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'ilike', "%{$search}%")
                  ->orWhere('description', 'ilike', "%{$search}%")
                  ->orWhere('version', 'ilike', "%{$search}%");
            });
        }

        if ($request->filled('areas')) {
            $areas         = (array) $request->get('areas');
            $includeNoArea = \in_array('0', $areas) || \in_array(0, $areas);
            $areaIds       = array_values(array_filter($areas, fn ($a) => $a != '0' && $a != 0));

            $query->where(function ($q) use ($areaIds, $includeNoArea) {
                if ($areaIds) $q->whereIn('document_area_id', $areaIds);
                if ($includeNoArea) $q->orWhereNull('document_area_id');
            });
        }

        $documents = $query->paginate(10)->withQueryString();

        $documents->getCollection()->transform(function (Document $document) {
            $document->file_size   = $document->fileSize();
            $document->file_exists = $document->fileExists();
            $document->status      = $this->documentService->getDocumentStatus($document);
            return $document;
        });

        return Inertia::render('Admin/Documents/Index', [
            'documents'     => $documents,
            'documentAreas' => DocumentArea::orderBy('name')->get(['id', 'name']),
            'filters'       => $request->only(['search', 'areas']),
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
        $document->file_size   = $document->fileSize();
        $document->file_exists = $document->fileExists();

        return Inertia::render('Admin/Documents/Create', [
            'document'      => $document,
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
        if ($document->official_file_path && !str_starts_with($document->official_file_path, 'http')) {
            Storage::disk('public')->delete($document->official_file_path);
        }

        $document->delete();

        return redirect()->route('admin.documents.index')
            ->with('success', 'Dokumen berhasil dihapus.');
    }

    public function toggleVisibility(Document $document): RedirectResponse
    {
        $document->update(['is_public' => !$document->is_public]);

        $status = $document->is_public ? 'dipublikasikan' : 'disembunyikan dari publik';

        return back()->with('success', "Dokumen berhasil {$status}.");
    }
}
