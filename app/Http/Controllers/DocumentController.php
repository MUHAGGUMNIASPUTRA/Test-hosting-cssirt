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
    $query = Document::published()
      ->where(function($q) {
      $q->where('version', '!=', 'RFC2350')
        ->orWhereNull('version');
    });

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
      $document->file_size = $document->fileSize();
      $document->file_exists = $document->fileExists();
      return $document;
    });

    return $this->handleSeoRequest('Documents/Index', [
      'documents' => $documents,
      'filters' => $request->only(['search']),
    ]);
  }

  /**
   * Download a document
   */
  public function download(Request $request, Document $document)
  {
    if (!$document->fileExists()) {
      abort(404, 'File tidak ditemukan');
    }

    return response()->download($document->downloadUrl(), $document->title . '.pdf', [
      'Content-Type' => 'application/pdf',
    ]);
  }

  /**
   * View a document in browser
   */
  public function view(Request $request, Document $document)
  {
    if (!$document->fileExists()) {
      abort(404, 'File tidak ditemukan');
    }

    return response()->file($document->downloadUrl(), [
      'Content-Type' => 'application/pdf',
      'Content-Disposition' => 'inline; filename="' . $document->title . '.pdf"'
    ]);
  }
}
