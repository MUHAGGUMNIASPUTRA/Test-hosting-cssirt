<?php

// filepath: app/Http/Controllers/RFC2350Controller.php

namespace App\Http\Controllers;

use App\Http\Traits\HandlesSeoRequests;
use App\Models\Document;
use Illuminate\Http\Request;

class RFC2350Controller extends Controller
{
    use HandlesSeoRequests;

    /**
     * Display the RFC 2350 page
     */
    public function index(Request $request)
    {
        // Get the RFC 2350 document from documents table
        $rfc2350Document = Document::where('version', 'RFC2350')->first();

        // Add file size and existence check
        if ($rfc2350Document) {
            $rfc2350Document->file_size = $rfc2350Document->fileSize();
            $rfc2350Document->file_exists = $rfc2350Document->fileExists();
        }

        return $this->handleSeoRequest('RFC2350/Index', [
            'document' => $rfc2350Document,
        ]);
    }

    /**
     * View the RFC 2350 document in browser
     */
    public function view(Request $request)
    {
        $document = Document::where('version', 'RFC2350')->firstOrFail();

        if (! $document->fileExists()) {
            abort(404, 'File tidak ditemukan');
        }

        return response()->file($document->downloadUrl(), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="RFC2350-CSIRT-Bojonegoro.pdf"',
        ]);
    }

    /**
     * Download the RFC 2350 document
     */
    public function download(Request $request)
    {
        $document = Document::where('version', 'RFC2350')->firstOrFail();

        if (! $document->fileExists()) {
            abort(404, 'File tidak ditemukan');
        }

        return response()->download($document->downloadUrl(), 'RFC2350-CSIRT-Bojonegoro.pdf', [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
