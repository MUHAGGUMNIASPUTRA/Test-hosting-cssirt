<?php

namespace App\Http\Controllers\Admin\Assets;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Assets\SaveSecurityNoteRequest;
use App\Models\AssetSecurityNote;
use App\Models\License;
use App\Models\MobileApplication;
use App\Models\WebApplication;
use App\Services\AttachmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AssetSecurityNoteController extends Controller
{
    public function __construct(private readonly AttachmentService $attachmentService) {}

    public function store(SaveSecurityNoteRequest $request, string $assetType, string $assetId): JsonResponse
    {
        $attachment = null;
        if ($request->filled('attachment_type')) {
            $attachment = $this->attachmentService->resolve(
                file: $request->file('attachment_file'),
                type: $request->input('attachment_type'),
                linkValue: $request->input('attachment_link'),
                existing: null,
                disk: 'public',
                directory: 'assets/security-notes',
            );
        }

        AssetSecurityNote::create([
            'asset_type' => $this->morphType($assetType),
            'asset_id' => $assetId,
            'user_id' => Auth::id(),
            'message' => $request->input('message'),
            'attachment_id' => $attachment?->id,
        ]);

        return response()->json(['message' => 'Catatan keamanan berhasil ditambahkan.'], 201);
    }

    public function update(SaveSecurityNoteRequest $request, AssetSecurityNote $securityNote): JsonResponse
    {
        $this->authorize('update', $securityNote);

        if ($request->boolean('remove_attachment')) {
            $this->attachmentService->delete($securityNote->attachment);
            $attachment = null;
        } else {
            $attachment = $this->attachmentService->resolve(
                file: $request->file('attachment_file'),
                type: $request->input('attachment_type'),
                linkValue: $request->input('attachment_link'),
                existing: $securityNote->attachment,
                disk: 'public',
                directory: 'assets/security-notes',
            );
        }

        $securityNote->update([
            'message' => $request->input('message'),
            'attachment_id' => $attachment?->id,
        ]);

        return response()->json(['message' => 'Catatan keamanan berhasil diperbarui.']);
    }

    public function destroy(AssetSecurityNote $securityNote): JsonResponse
    {
        $this->authorize('delete', $securityNote);

        $this->attachmentService->delete($securityNote->attachment);
        $securityNote->delete();

        return response()->json(['message' => 'Catatan keamanan berhasil dihapus.']);
    }

    private function morphType(string $type): string
    {
        return match ($type) {
            'web-application' => WebApplication::class,
            'mobile-application' => MobileApplication::class,
            'license' => License::class,
            'physical-asset' => PhysicalAsset::class,
            default => abort(404),
        };
    }
}
