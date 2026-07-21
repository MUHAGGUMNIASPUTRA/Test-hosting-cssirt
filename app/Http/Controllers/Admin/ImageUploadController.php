<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreEditorImageRequest;
use App\Services\AttachmentService;
use Illuminate\Support\Facades\Storage;

class ImageUploadController extends Controller
{
    public function __construct(private readonly AttachmentService $attachmentService) {}

    // Tujuan: Simpan gambar editor Tiptap dengan normalisasi
    // Caller: Tiptap editor (frontend)
    // Side Effects: File storage I/O (write to disk/public/posts-editor)
    public function store(StoreEditorImageRequest $request)
    {
        $path = $this->attachmentService->storeNormalized($request->file('image'), 'public', 'posts-editor');

        return response()->json(['url' => Storage::url($path)]);
    }
}
