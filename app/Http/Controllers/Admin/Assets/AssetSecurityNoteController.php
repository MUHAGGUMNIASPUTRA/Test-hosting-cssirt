<?php

namespace App\Http\Controllers\Admin\Assets;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Assets\SaveSecurityNoteRequest;
use App\Models\AssetSecurityNote;
use App\Models\MobileApplication;
use App\Models\WebApplication;
use App\Services\AttachmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class AssetSecurityNoteController extends Controller
{
    public function __construct(private readonly AttachmentService $attachmentService) {}

    public function store(SaveSecurityNoteRequest $request, string $assetType, string $assetId): RedirectResponse
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

        return redirect()->back()->with('success', 'Catatan keamanan berhasil ditambahkan.');
    }

    public function update(SaveSecurityNoteRequest $request, AssetSecurityNote $securityNote): RedirectResponse
    {
        $this->authorize('update', $securityNote);

        $attachment = $this->attachmentService->resolve(
            file: $request->file('attachment_file'),
            type: $request->input('attachment_type'),
            linkValue: $request->input('attachment_link'),
            existing: $securityNote->attachment,
            disk: 'public',
            directory: 'assets/security-notes',
        );

        $securityNote->update([
            'message' => $request->input('message'),
            'attachment_id' => $attachment?->id,
        ]);

        return redirect()->back()->with('success', 'Catatan keamanan berhasil diperbarui.');
    }

    public function destroy(AssetSecurityNote $securityNote): RedirectResponse
    {
        $this->authorize('delete', $securityNote);

        $this->attachmentService->delete($securityNote->attachment);
        $securityNote->delete();

        return redirect()->back()->with('success', 'Catatan keamanan berhasil dihapus.');
    }

    private function morphType(string $type): string
    {
        return match ($type) {
            'web-application' => WebApplication::class,
            'mobile-application' => MobileApplication::class,
            default => abort(404),
        };
    }
}
