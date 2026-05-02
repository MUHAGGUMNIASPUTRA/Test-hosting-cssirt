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
        $query = Document::with(['documentArea:id,name', 'officialAttachment'])
            ->published()
            ->where(function ($q) {
                $q->where('version', '!=', 'RFC2350');
            })
            ->orderBy('title');

        // Apply search filter
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'ilike', '%'.$request->search.'%')
                    ->orWhere('description', 'ilike', '%'.$request->search.'%')
                    ->orWhere('version', 'ilike', '%'.$request->search.'%');
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
        $document->loadMissing('officialAttachment');
        $attachment = $document->officialAttachment;

        if (! $attachment) {
            abort(404, 'File tidak ditemukan');
        }

        if ($attachment->isLink()) {
            abort(400, 'Dokumen ini berupa tautan eksternal dan tidak dapat diunduh');
        }

        if (! Storage::disk($attachment->disk)->exists($attachment->path)) {
            abort(404, 'File tidak ditemukan');
        }

        return response()->download(
            Storage::disk($attachment->disk)->path($attachment->path),
            $document->title.'.pdf',
            ['Content-Type' => 'application/pdf'],
        );
    }

    /**
     * View a document in browser — redirect jika berupa link, tampilkan file jika berupa PDF
     */
    public function view(Request $request, Document $document)
    {
        $document->loadMissing('officialAttachment');
        $attachment = $document->officialAttachment;

        if (! $attachment) {
            abort(404, 'File tidak ditemukan');
        }

        if ($attachment->isLink()) {
            return redirect($attachment->url);
        }

        if (! Storage::disk($attachment->disk)->exists($attachment->path)) {
            abort(404, 'File tidak ditemukan');
        }

        return response()->file(Storage::disk($attachment->disk)->path($attachment->path), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$document->title.'.pdf"',
        ]);
    }
}
