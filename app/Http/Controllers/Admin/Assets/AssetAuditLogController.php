<?php

namespace App\Http\Controllers\Admin\Assets;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Assets\SaveAuditLogRequest;
use App\Models\AssetAuditLog;
use App\Services\AttachmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class AssetAuditLogController extends Controller
{
    public function __construct(private readonly AttachmentService $attachmentService) {}

    public function store(SaveAuditLogRequest $request, string $assetType, string $assetId): RedirectResponse
    {
        $attachment = null;
        if ($request->filled('attachment_type')) {
            $attachment = $this->attachmentService->resolve(
                file: $request->file('attachment_file'),
                type: $request->input('attachment_type'),
                linkValue: $request->input('attachment_link'),
                existing: null,
                disk: 'public',
                directory: 'assets/audit-logs',
            );
        }

        AssetAuditLog::create([
            'asset_type' => $assetType,
            'asset_id' => $assetId,
            'user_id' => Auth::id(),
            'message' => $request->input('message'),
            'danger_level' => $request->input('danger_level'),
            'attachment_id' => $attachment?->id,
        ]);

        return redirect()->back()->with('success', 'Catatan audit berhasil ditambahkan.');
    }

    public function update(SaveAuditLogRequest $request, AssetAuditLog $auditLog): RedirectResponse
    {
        $this->authorize('update', $auditLog);

        $attachment = $this->attachmentService->resolve(
            file: $request->file('attachment_file'),
            type: $request->input('attachment_type'),
            linkValue: $request->input('attachment_link'),
            existing: $auditLog->attachment,
            disk: 'public',
            directory: 'assets/audit-logs',
        );

        $auditLog->update([
            'message' => $request->input('message'),
            'danger_level' => $request->input('danger_level'),
            'attachment_id' => $attachment?->id,
        ]);

        return redirect()->back()->with('success', 'Catatan audit berhasil diperbarui.');
    }

    public function destroy(AssetAuditLog $auditLog): RedirectResponse
    {
        $this->authorize('delete', $auditLog);

        $this->attachmentService->delete($auditLog->attachment);
        $auditLog->delete();

        return redirect()->back()->with('success', 'Catatan audit berhasil dihapus.');
    }
}
