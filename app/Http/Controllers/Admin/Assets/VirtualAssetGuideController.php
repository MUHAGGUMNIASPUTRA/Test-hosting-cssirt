<?php

namespace App\Http\Controllers\Admin\Assets;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Assets\SaveVirtualAssetGuideRequest;
use App\Models\VirtualAssetGuide;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class VirtualAssetGuideController extends Controller
{
    public function __construct() {}

    public function index(Request $request): Response
    {
        $query = VirtualAssetGuide::withCount('guideAttachments')->latest();

        if ($request->filled('search')) {
            $query->where('name', 'ilike', '%'.$request->search.'%');
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $guides = $query->orderBy('type', 'desc')->orderBy('name')->paginate(15)->withQueryString();

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

        $this->syncDocuments($guide, $request->input('document_ids', []));

        return redirect()->route('admin.virtual-asset-guides.index')
            ->with('success', 'Panduan berhasil ditambahkan.');
    }

    public function edit(VirtualAssetGuide $virtualAssetGuide): Response
    {
        return Inertia::render('Admin/Assets/VirtualAssetGuides/Create', [
            'guide' => $virtualAssetGuide->load(['guideAttachments.document.officialAttachment']),
        ]);
    }

    public function update(SaveVirtualAssetGuideRequest $request, VirtualAssetGuide $virtualAssetGuide): RedirectResponse
    {
        $virtualAssetGuide->update($request->only(['name', 'description', 'type']));

        $this->syncDocuments($virtualAssetGuide, $request->input('document_ids', []));

        return redirect()->route('admin.virtual-asset-guides.index')
            ->with('success', 'Panduan berhasil diperbarui.');
    }

    public function destroy(VirtualAssetGuide $virtualAssetGuide): RedirectResponse
    {
        $virtualAssetGuide->delete();

        return redirect()->back()->with('success', 'Panduan berhasil dihapus.');
    }

    private function syncDocuments(VirtualAssetGuide $guide, array $documentIds): void
    {
        // Delete pivot records not in the new list
        $guide->guideAttachments()->whereNotIn('document_id', $documentIds)->delete();

        // Update or create pivot records with sort_order
        foreach ($documentIds as $index => $docId) {
            $guide->guideAttachments()->updateOrCreate(
                ['document_id' => $docId],
                ['sort_order' => $index + 1]
            );
        }
    }
}
