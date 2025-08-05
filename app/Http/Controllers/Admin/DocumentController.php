<?php
// filepath: app/Http/Controllers/Admin/DocumentController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class DocumentController extends Controller
{
  /**
   * Display a listing of the documents.
   */
  public function index(Request $request): Response
  {
    $query = Document::query()->latest();

    // Apply search filter
    if ($request->filled('search')) {
      $query->where('title', 'ilike', '%' . $request->search . '%')
            ->orWhere('description', 'ilike', '%' . $request->search . '%')
            ->orWhere('version', 'ilike', '%' . $request->search . '%');
    }

    // Apply status filter
    if ($request->filled('status')) {
      if ($request->status === 'published') {
        $query->whereNotNull('published_at')
              ->where('published_at', '<=', now());
      } else {
        $query->where(function ($q) {
            $q->whereNull('published_at')
              ->orWhere('published_at', '>', now());
        });
      }
    }

    $documents = $query->orderBy('title')->paginate(10)->withQueryString();

    // Add file size and status to each document
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

  /**
   * Show the form for creating a new document.
   */
  public function create(): Response
  {
    return Inertia::render('Admin/Documents/Create');
  }

  /**
   * Store a newly created document in storage.
   */
  public function store(Request $request)
  {
    $validated = $request->validate([
      'title' => 'required|string|max:255',
      'description' => 'nullable|string',
      'version' => 'nullable|string|max:255',
      'file' => 'required|file|mimes:pdf|max:8192', // Max 8MB
      'published_at' => 'nullable|date',
    ]);

    // Store the file
    $path = $request->file('file')->store('documents', 'public');

    Document::create([
      'title' => $validated['title'],
      'slug' => Str::slug($validated['title']),
      'description' => $validated['description'],
      'file_path' => $path,
      'version' => $validated['version'],
      'published_at' => now(),
    ]);

    return redirect()->route('admin.documents.index')->with('success', 'Dokumen berhasil ditambahkan.');
  }

  /**
   * Show the form for editing the specified document.
   */
  public function edit(Document $document): Response
  {
    $document->file_size = $document->fileSize();
    $document->file_exists = $document->fileExists();

    return Inertia::render('Admin/Documents/Create', [
      'document' => $document,
    ]);
  }

  /**
   * Update the specified document in storage.
   */
  public function update(Request $request, Document $document)
  {
    $validated = $request->validate([
      'title' => 'required|string|max:255',
      'description' => 'nullable|string',
      'version' => 'nullable|string|max:255',
      'file' => 'nullable|file|mimes:pdf|max:10240', // Max 10MB
      'published_at' => 'nullable|date',
    ]);

    $path = $document->file_path;
    if ($request->hasFile('file')) {
      // Delete old file if it exists
      if ($document->fileExists()) {
        Storage::disk('public')->delete($document->file_path);
      }
      $path = $request->file('file')->store('documents', 'public');
    }

    $document->update([
      'title' => $validated['title'],
      'slug' => Str::slug($validated['title']),
      'description' => $validated['description'],
      'file_path' => $path,
      'version' => $validated['version'],
      'published_at' => now(),
    ]);

    return redirect()->route('admin.documents.index')->with('success', 'Dokumen berhasil diperbarui.');
  }

  /**
   * Remove the specified document from storage.
   */
  public function destroy(Document $document)
  {
    // Delete the file
    if ($document->fileExists()) {
      Storage::disk('public')->delete($document->file_path);
    }

    $document->delete();

    return redirect()->route('admin.documents.index')->with('success', 'Dokumen berhasil dihapus.');
  }

  /**
   * Get document status
   */
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
