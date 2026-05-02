<?php

// Tujuan: CRUD aset informasi dengan klasifikasi keamanan
// Caller: routes/web.php admin group
// Side Effects: DB write

namespace App\Http\Controllers\Admin\Assets;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Assets\SaveInformationAssetRequest;
use App\Models\Document;
use App\Models\InformationAsset;
use App\Models\Location;
use App\Models\Organization;
use App\Services\Assets\InformationAssetService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class InformationAssetController extends Controller
{
    public function __construct(private readonly InformationAssetService $service) {}

    public function index(): Response
    {
        $assets = InformationAsset::with(['document', 'ownerOrg', 'location', 'securityClassification'])
            ->when(request('search'), fn ($q, $s) => $q->whereHas('document', fn ($dq) => $dq->where('title', 'ilike', "%{$s}%")))
            ->when(request('owner_org_id'), fn ($q, $v) => $q->where('owner_org_id', $v))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Assets/InformationAssets/Index', [
            'informationAssets' => $assets,
            'organizations' => Organization::orderBy('name')->get(['id', 'name']),
            'filters' => request()->only(['search', 'owner_org_id']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Assets/InformationAssets/Create', $this->formData());
    }

    public function store(SaveInformationAssetRequest $request): RedirectResponse
    {
        $asset = $this->service->create($request->validated());

        return redirect()->route('admin.information-assets.show', $asset)
            ->with('success', 'Aset informasi berhasil ditambahkan.');
    }

    public function show(InformationAsset $informationAsset): Response
    {
        $informationAsset->load([
            'document', 'location', 'ownerOrg',
            'securityClassification',
            'securityNotes.user', 'securityNotes.attachment',
            'auditLogs.user',
        ]);

        return Inertia::render('Admin/Assets/InformationAssets/Show', [
            'asset' => $informationAsset,
        ]);
    }

    public function edit(InformationAsset $informationAsset): Response
    {
        $informationAsset->load([
            'document', 'location', 'ownerOrg',
            'securityClassification',
            'securityNotes.user', 'securityNotes.attachment',
            'auditLogs.user',
        ]);

        return Inertia::render('Admin/Assets/InformationAssets/Create', [
            'asset' => $informationAsset,
            ...$this->formData(),
        ]);
    }

    public function update(SaveInformationAssetRequest $request, InformationAsset $informationAsset): RedirectResponse
    {
        $this->service->update($informationAsset, $request->validated());

        return redirect()->route('admin.information-assets.show', $informationAsset)
            ->with('success', 'Aset informasi berhasil diperbarui.');
    }

    public function destroy(InformationAsset $informationAsset): RedirectResponse
    {
        $this->service->delete($informationAsset);

        return redirect()->route('admin.information-assets.index')
            ->with('success', 'Aset informasi berhasil dihapus.');
    }

    private function formData(): array
    {
        return [
            'documents' => Document::where('is_public', true)->orderBy('title')->get(['id', 'title', 'slug']),
            'organizations' => Organization::orderBy('name')->get(['id', 'name']),
            'locations' => Location::orderBy('name')->get(['id', 'name']),
        ];
    }
}
