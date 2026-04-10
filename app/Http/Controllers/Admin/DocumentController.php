<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentArea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class DocumentController extends Controller
{
  public function index(Request $request): Response
  {
    $query = Document::with('documentArea:id,name')->orderBy('title');

    if ($request->filled('search')) {
      $query->where(function ($q) use ($request) {
        $q->where('title', 'ilike', '%' . $request->search . '%')
          ->orWhere('description', 'ilike', '%' . $request->search . '%')
          ->orWhere('version', 'ilike', '%' . $request->search . '%');
      });
    }

    if ($request->filled('areas')) {
      $query->whereIn('document_area_id', (array) $request->areas);
    }

    $documents = $query->paginate(10)->withQueryString();

    $documents->getCollection()->transform(function ($document) {
      $document->file_size = $document->fileSize();
      $document->file_exists = $document->fileExists();
      $document->status = $this->getDocumentStatus($document);
      return $document;
    });

    return Inertia::render('Admin/Documents/Index', [
      'documents' => $documents,
      'documentAreas' => DocumentArea::orderBy('name')->get(['id', 'name']),
      'filters' => $request->only(['search', 'areas']),
    ]);
  }

  public function create(): Response
  {
    return Inertia::render('Admin/Documents/Create', [
      'documentAreas' => DocumentArea::orderBy('name')->get(['id', 'name']),
    ]);
  }

  public function store(Request $request)
  {
    $validated = $request->validate([
      'title' => 'required|string|max:255',
      'description' => 'nullable|string',
      'version' => 'nullable|string|max:50',
      'published_at' => 'nullable|date',
      'is_public' => 'boolean',
      'document_area_id' => 'nullable|exists:document_areas,id',
      // File Dokumen (Word — link saja, hanya admin)
      'doc_file_link' => 'nullable|url|max:2000',
      // File Dokumen Sah (PDF — upload atau link, wajib)
      'official_file_type' => 'required|in:file,link',
      'official_file' => 'nullable|file|mimes:pdf|max:51200',
      'official_file_link' => 'nullable|url|max:2000',
    ], [
      'official_file_type.required' => 'Jenis File Dokumen Sah wajib dipilih.',
      'official_file.mimes' => 'File Dokumen Sah harus berupa PDF.',
      'official_file.max' => 'Ukuran File Dokumen Sah maksimal 50MB.',
      'doc_file_link.url' => 'Link File Dokumen harus berupa URL yang valid.',
      'official_file_link.url' => 'Link File Dokumen Sah harus berupa URL yang valid.',
    ]);

    $officialFilePath = $this->resolveOfficialFile($request, $validated);

    Document::create([
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

  public function update(Request $request, Document $document)
  {
    $validated = $request->validate([
      'title' => 'required|string|max:255',
      'description' => 'nullable|string',
      'version' => 'nullable|string|max:50',
      'published_at' => 'nullable|date',
      'is_public' => 'boolean',
      'document_area_id' => 'nullable|exists:document_areas,id',
      'doc_file_link' => 'nullable|url|max:2000',
      'official_file_type' => 'required|in:file,link',
      'official_file' => 'nullable|file|mimes:pdf|max:51200',
      'official_file_link' => 'nullable|url|max:2000',
    ], [
      'official_file_type.required' => 'Jenis File Dokumen Sah wajib dipilih.',
      'official_file.mimes' => 'File Dokumen Sah harus berupa PDF.',
      'official_file.max' => 'Ukuran File Dokumen Sah maksimal 50MB.',
      'doc_file_link.url' => 'Link File Dokumen harus berupa URL yang valid.',
      'official_file_link.url' => 'Link File Dokumen Sah harus berupa URL yang valid.',
    ]);

    $officialFilePath = $this->resolveOfficialFile($request, $validated, $document);

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

    return redirect()->route('admin.documents.index')
      ->with('success', 'Dokumen berhasil diperbarui.');
  }

  public function destroy(Document $document)
  {
    if ($document->official_file_path && !str_starts_with($document->official_file_path, 'http')) {
      Storage::disk('public')->delete($document->official_file_path);
    }

    $document->delete();

    return redirect()->route('admin.documents.index')
      ->with('success', 'Dokumen berhasil dihapus.');
  }

  public function toggleVisibility(Document $document)
  {
    $document->update(['is_public' => !$document->is_public]);

    $status = $document->is_public ? 'dipublikasikan' : 'disembunyikan dari publik';

    return back()->with('success', "Dokumen berhasil {$status}.");
  }

  private function resolveOfficialFile(Request $request, array $validated, ?Document $existing = null): ?string
  {
    if ($validated['official_file_type'] === 'file') {
      if ($request->hasFile('official_file')) {
        // Hapus file lama jika ada
        if ($existing?->official_file_path && !str_starts_with($existing->official_file_path, 'http')) {
          Storage::disk('public')->delete($existing->official_file_path);
        }
        return $request->file('official_file')->store('documents/official', 'public');
      }
      // Tidak ada file baru — pertahankan yang lama
      return $existing?->official_file_path;
    }

    // mode link
    return !empty($validated['official_file_link']) ? $validated['official_file_link'] : $existing?->official_file_path;
  }

  private function getDocumentStatus($document): string
  {
    if (!$document->published_at) {
      return 'Draft';
    }

    if ($document->published_at > now()) {
      return 'Scheduled';
    }

    return 'Published';
  }
}
