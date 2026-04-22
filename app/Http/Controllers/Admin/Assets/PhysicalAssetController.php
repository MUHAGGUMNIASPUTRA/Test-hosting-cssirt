<?php

// Tujuan: CRUD aset fisik dengan halaman create/edit dan show terpisah
// Caller: routes/web.php admin group
// Side Effects: DB write, storage I/O (attachment)

namespace App\Http\Controllers\Admin\Assets;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Assets\SavePhysicalAssetRequest;
use App\Models\Employee;
use App\Models\Location;
use App\Models\Organization;
use App\Models\PhysicalAsset;
use App\Services\Assets\PhysicalAssetService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PhysicalAssetController extends Controller
{
    public function __construct(private readonly PhysicalAssetService $service) {}

    public function index(): Response
    {
        $assets = PhysicalAsset::with(['ownerOrg', 'location'])
            ->when(request('search'), fn ($q, $s) => $q->where(function ($q) use ($s) {
                $q->where('name', 'ilike', "%{$s}%")->orWhere('asset_code', 'ilike', "%{$s}%");
            }))
            ->when(request('owner_org_id'), fn ($q, $v) => $q->where('owner_org_id', $v))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Assets/PhysicalAssets/Index', [
            'physicalAssets' => $assets,
            'organizations' => Organization::orderBy('name')->get(['id', 'name']),
            'filters' => request()->only(['search', 'owner_org_id']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Assets/PhysicalAssets/Create', $this->formData());
    }

    public function store(SavePhysicalAssetRequest $request): RedirectResponse
    {
        $asset = $this->service->create($request->validated(), $request->file('attachment'));

        return redirect()->route('admin.physical-assets.show', $asset)
            ->with('success', 'Aset fisik berhasil ditambahkan.');
    }

    public function show(PhysicalAsset $physicalAsset): Response
    {
        $physicalAsset->load(['location', 'ownerOrg', 'ownerEmployee', 'attachment']);

        return Inertia::render('Admin/Assets/PhysicalAssets/Show', [
            'physicalAsset' => $physicalAsset,
        ]);
    }

    public function edit(PhysicalAsset $physicalAsset): Response
    {
        $physicalAsset->load(['location', 'ownerOrg', 'ownerEmployee', 'attachment']);

        return Inertia::render('Admin/Assets/PhysicalAssets/Create', [
            'asset' => $physicalAsset,
            ...$this->formData(),
        ]);
    }

    public function update(SavePhysicalAssetRequest $request, PhysicalAsset $physicalAsset): RedirectResponse
    {
        $this->service->update($physicalAsset, $request->validated(), $request->file('attachment'));

        return redirect()->route('admin.physical-assets.show', $physicalAsset)
            ->with('success', 'Aset fisik berhasil diperbarui.');
    }

    public function destroy(PhysicalAsset $physicalAsset): RedirectResponse
    {
        $this->service->delete($physicalAsset);

        return redirect()->route('admin.physical-assets.index')
            ->with('success', 'Aset fisik berhasil dihapus.');
    }

    private function formData(): array
    {
        return [
            'organizations' => Organization::orderBy('name')->get(['id', 'name', 'it_contact_name', 'it_contact_phone']),
            'locations' => Location::with('organization')->orderBy('name')->get(['id', 'name', 'organization_id']),
            'employees' => Employee::where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ];
    }
}
