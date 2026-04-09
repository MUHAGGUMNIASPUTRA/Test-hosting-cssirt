<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class DocumentController extends Controller
{
  public function index(Request $request): Response
  {
    $query = Document::query()->orderBy('title');

    if ($request->filled('search')) {
      $query->where(function ($q) use ($request) {
        $q->where('title', 'ilike', '%' . $request->search . '%')
          ->orWhere('description', 'ilike', '%' . $request->search . '%')
          ->orWhere('version', 'ilike', '%' . $request->search . '%');
      });
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
      'filters' => $request->only(['search', 'status']),
    ]);
  }

  public function create(): Response
  {
    return Inertia::render('Admin/Documents/Create');
  }

  public function store(Request $request)
  {
    $validated = $request->validate([
      'title' => 'required|string|max:255',
      'description' => 'nullable|string',
      'version' => 'nullable|string|max:50',
      'published_at' => 'nullable|date',
      'file_type' => 'required|in:file,link',
      'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,txt|max:20480',
      'file_links' => 'nullable|string|max:2000',
    ]);

    $filePath = null;
    if ($validated['file_type'] === 'file' && $request->hasFile('file')) {
      $filePath = $request->file('file')->store('documents', 'public');
    } elseif ($validated['file_type'] === 'link' && !empty($validated['file_links'])) {
      $filePath = $validated['file_links'];
    }

    Document::create([
      'title' => $validated['title'],
      'slug' => Str::slug($validated['title']),
      'description' => $validated['description'] ?? null,
      'file_path' => $filePath,
      'version' => $validated['version'] ?? null,
      'published_at' => $validated['published_at'] ?? null,
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
    ]);
  }

  public function update(Request $request, Document $document)
  {
    $validated = $request->validate([
      'title' => 'required|string|max:255',
      'description' => 'nullable|string',
      'version' => 'nullable|string|max:50',
      'published_at' => 'nullable|date',
      'file_type' => 'required|in:file,link',
      'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,txt|max:20480',
      'file_links' => 'nullable|string|max:2000',
    ]);

    $filePath = $document->file_path;
    if ($validated['file_type'] === 'file' && $request->hasFile('file')) {
      if ($document->file_path && !str_starts_with($document->file_path, 'http')) {
        Storage::disk('public')->delete($document->file_path);
      }
      $filePath = $request->file('file')->store('documents', 'public');
    } elseif ($validated['file_type'] === 'link') {
      $filePath = !empty($validated['file_links']) ? $validated['file_links'] : null;
    }

    $document->update([
      'title' => $validated['title'],
      'slug' => Str::slug($validated['title']),
      'description' => $validated['description'] ?? null,
      'file_path' => $filePath,
      'version' => $validated['version'] ?? null,
      'published_at' => $validated['published_at'] ?? null,
    ]);

    return redirect()->route('admin.documents.index')
      ->with('success', 'Dokumen berhasil diperbarui.');
  }

  public function destroy(Document $document)
  {
    if ($document->file_path && !str_starts_with($document->file_path, 'http')) {
      Storage::disk('public')->delete($document->file_path);
    }

    $document->delete();

    return redirect()->route('admin.documents.index')
      ->with('success', 'Dokumen berhasil dihapus.');
  }

  private function getDocumentStatus($document)
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
