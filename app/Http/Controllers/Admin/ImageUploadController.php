<?php
// File: app/Http/Controllers/Admin/ImageUploadController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageUploadController extends Controller
{
  /**
   * Store an image uploaded from the Tiptap editor.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(Request $request)
  {
    $request->validate([
      'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // Max 2MB
    ]);

    if ($request->hasFile('image')) {
      $path = $request->file('image')->store('editor-images', 'public');

      // Return the public URL of the uploaded image
      return response()->json(['url' => Storage::url($path)]);
    }

    return response()->json(['error' => 'Gagal mengunggah gambar.'], 422);
  }
}
