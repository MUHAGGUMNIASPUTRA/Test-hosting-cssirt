<?php
// filepath: app/Http/Controllers/DocumentController.php
namespace App\Http\Controllers;

use App\Http\Traits\HandlesSeoRequests;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
  use HandlesSeoRequests;

  /**
   * Display a listing of published documents
   */
  public function index(Request $request)
  {
    $query = Document::published()->where('version', '!=', 'RFC2350');

    // Apply search filter
    if ($request->filled('search')) {
      $query->where(function($q) use ($request) {
          $q->where('title', 'ilike', '%' . $request->search . '%')
            ->orWhere('description', 'ilike', '%' . $request->search . '%')
            ->orWhere('version', 'ilike', '%' . $request->search . '%');
      });
    }

    $documents = $query->orderBy('title')->paginate(10)->withQueryString();

    // Add file size to each document
    $documents->getCollection()->transform(function ($document) {
      $document->file_size = $this->getFileSize($document->file_path);
      $document->file_exists = Storage::disk('public')->exists($document->file_path);
      return $document;
    });

    return $this->handleSeoRequest('Documents/Index', [
      'documents' => $documents,
      'filters' => $request->only(['search', 'sortField', 'sortOrder']),
    ]);
  }

  /**
   * Download a document
   */
  public function download(Request $request, Document $document)
  {
    // Check if document is published
    if (!$document->published_at || $document->published_at > now()) {
      abort(404);
    }

    // Check if file exists
    if (!Storage::disk('public')->exists($document->file_path)) {
      abort(404, 'File tidak ditemukan');
    }

    $filePath = Storage::disk('public')->path($document->file_path);

    return response()->download($filePath, $document->title . '.pdf', [
      'Content-Type' => 'application/pdf',
    ]);
  }

  /**
   * View a document in browser
   */
  public function view(Request $request, Document $document)
  {
    // Check if document is published
    if (!$document->published_at || $document->published_at > now()) {
      abort(404);
    }

    // Check if file exists
    if (!Storage::disk('public')->exists($document->file_path)) {
      abort(404, 'File tidak ditemukan');
    }

    $filePath = Storage::disk('public')->path($document->file_path);

    return response()->file($filePath, [
      'Content-Type' => 'application/pdf',
      'Content-Disposition' => 'inline; filename="' . $document->title . '.pdf"'
    ]);
  }

  /**
   * Get file size in human readable format
   */
  private function getFileSize($filePath)
  {
    if (Storage::disk('public')->exists($filePath)) {
      $bytes = Storage::disk('public')->size($filePath);
      return $this->formatBytes($bytes);
    }
    return 'File not found';
  }

  /**
   * Format bytes to human readable format
   */
  private function formatBytes($bytes, $precision = 2)
  {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');

    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
      $bytes /= 1024;
    }

    return round($bytes, $precision) . ' ' . $units[$i];
  }
}
