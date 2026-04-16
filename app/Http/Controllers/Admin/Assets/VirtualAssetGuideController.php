<?php

namespace App\Http\Controllers\Admin\Assets;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Assets\SaveVirtualAssetGuideRequest;
use App\Models\VirtualAssetGuide;
use App\Models\VirtualAssetGuideAttachment;
use App\Services\AttachmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class VirtualAssetGuideController extends Controller
{
    public function __construct(private readonly AttachmentService $attachmentService) {}

    public function index(Request $request): Response
    {
        $query = VirtualAssetGuide::withCount('guideAttachments')->latest();

        if ($request->filled('search')) {
            $query->where('name', 'ilike', '%'.$request->search.'%');
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $guides = $query->paginate(15)->withQueryString();

        return Inertia::render('Admin/Assets/VirtualAssetGuides/Index', [
            'guides' => $guides,
            'filters' => $request->only(['search', 'type']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Assets/VirtualAssetGuides/Create');
    }

    public function store(SaveVirtualAssetGuideRequest $request): RedirectResponse
    {
        $guide = VirtualAssetGuide::create($request->only(['name', 'description', 'type']));

        $this->syncAttachments($guide, $request);

        return redirect()->route('admin.virtual-asset-guides.index')
            ->with('success', 'Panduan berhasil ditambahkan.');
    }

    public function edit(VirtualAssetGuide $virtualAssetGuide): Response
    {
        return Inertia::render('Admin/Assets/VirtualAssetGuides/Create', [
            'guide' => $virtualAssetGuide->load(['guideAttachments.attachment']),
        ]);
    }

    public function update(SaveVirtualAssetGuideRequest $request, VirtualAssetGuide $virtualAssetGuide): RedirectResponse
    {
        $virtualAssetGuide->update($request->only(['name', 'description', 'type']));

        $this->syncAttachments($virtualAssetGuide, $request);

        return redirect()->route('admin.virtual-asset-guides.index')
            ->with('success', 'Panduan berhasil diperbarui.');
    }

    public function destroy(VirtualAssetGuide $virtualAssetGuide): RedirectResponse
    {
        foreach ($virtualAssetGuide->guideAttachments as $guideAttachment) {
            $this->attachmentService->delete($guideAttachment->attachment);
        }
        $virtualAssetGuide->delete();

        return redirect()->back()->with('success', 'Panduan berhasil dihapus.');
    }

    private function syncAttachments(VirtualAssetGuide $guide, Request $request): void
    {
        $keepIds = $request->input('ordered_existing_ids', $request->input('existing_attachment_ids', []));

        // Delete removed attachments
        foreach ($guide->guideAttachments as $ga) {
            if (! in_array($ga->attachment_id, $keepIds)) {
                $this->attachmentService->delete($ga->attachment);
                $ga->delete();
            }
        }

        // Reorder existing attachments by position in keepIds array
        foreach ($keepIds as $index => $id) {
            $guide->guideAttachments()->where('attachment_id', $id)->update(['sort_order' => $index + 1]);
        }

        $nextSort = count($keepIds);

        // Append new file attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $attachment = $this->attachmentService->storeFile($file, 'public', 'guides/attachments');
                VirtualAssetGuideAttachment::create([
                    'virtual_asset_guide_id' => $guide->id,
                    'attachment_id' => $attachment->id,
                    'sort_order' => ++$nextSort,
                ]);
            }
        }

        // Append new link attachments
        foreach ($request->input('new_links', []) as $url) {
            if ($url) {
                $attachment = $this->attachmentService->storeLink($url);
                VirtualAssetGuideAttachment::create([
                    'virtual_asset_guide_id' => $guide->id,
                    'attachment_id' => $attachment->id,
                    'sort_order' => ++$nextSort,
                ]);
            }
        }
    }
}
