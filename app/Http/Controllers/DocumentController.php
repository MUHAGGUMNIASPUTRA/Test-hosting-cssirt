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
    $query = Document::with('documentArea:id,name')
      ->published()
      ->where(function($q) {
        $q->where('version', '!=', 'RFC2350');
      })
      ->orderBy('title');

    // Apply search filter
    if ($request->filled('search')) {
      $query->where(function($q) use ($request) {
          $q->where('title', 'ilike', '%' . $request->search . '%')
            ->orWhere('description', 'ilike', '%' . $request->search . '%')
            ->orWhere('version', 'ilike', '%' . $request->search . '%');
      });
    }

    $documents = $query->paginate(10)->withQueryString();

    return $this->handleSeoRequest('Documents/Index', [
      'documents' => $documents,
      'filters' => $request->only(['search']),
    ]);
  }

  /**
   * Download a document (hanya berlaku untuk file, bukan link)
   */
  public function download(Request $request, Document $document)
  {
    $path = $document->official_file_path;

    if (!$path) {
      abort(404, 'File tidak ditemukan');
    }

    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
      abort(400, 'Dokumen ini berupa tautan eksternal dan tidak dapat diunduh');
    }

    if (!Storage::disk('public')->exists($path)) {
      abort(404, 'File tidak ditemukan');
    }

    return response()->download(Storage::disk('public')->path($path), $document->title . '.pdf', [
      'Content-Type' => 'application/pdf',
    ]);
  }

  /**
   * View a document in browser — redirect jika berupa link, tampilkan file jika berupa PDF
   */
  public function view(Request $request, Document $document)
  {
    $path = $document->official_file_path;

    if (!$path) {
      abort(404, 'File tidak ditemukan');
    }

    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
      return redirect($path);
    }

    if (!Storage::disk('public')->exists($path)) {
      abort(404, 'File tidak ditemukan');
    }

    return response()->file(Storage::disk('public')->path($path), [
      'Content-Type' => 'application/pdf',
      'Content-Disposition' => 'inline; filename="' . $document->title . '.pdf"',
    ]);
  }
}
