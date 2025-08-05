<?php
// filepath: app/Http/Controllers/RFC2350Controller.php

namespace App\Http\Controllers;

use App\Http\Traits\HandlesSeoRequests;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
      $rfc2350Document->file_size = $this->getFileSize($rfc2350Document->file_path);
      $rfc2350Document->file_exists = Storage::disk('public')->exists($rfc2350Document->file_path);
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

    // Check if file exists
    if (!Storage::disk('public')->exists($document->file_path)) {
      abort(404, 'File tidak ditemukan');
    }

    $filePath = Storage::disk('public')->path($document->file_path);

    return response()->file($filePath, [
      'Content-Type' => 'application/pdf',
      'Content-Disposition' => 'inline; filename="RFC2350-CSIRT-Bojonegoro.pdf"'
    ]);
  }

  /**
   * Download the RFC 2350 document
   */
  public function download(Request $request)
  {
    $document = Document::where('version', 'RFC2350')->firstOrFail();

    // Check if file exists
    if (!Storage::disk('public')->exists($document->file_path)) {
      abort(404, 'File tidak ditemukan');
    }

    $filePath = Storage::disk('public')->path($document->file_path);

    return response()->download($filePath, 'RFC2350-CSIRT-Bojonegoro.pdf', [
        'Content-Type' => 'application/pdf',
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
