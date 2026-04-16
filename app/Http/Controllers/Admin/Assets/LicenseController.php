<?php

namespace App\Http\Controllers\Admin\Assets;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Assets\SaveLicenseRequest;
use App\Models\Employee;
use App\Models\License;
use App\Models\Location;
use App\Models\Organization;
use App\Services\Assets\LicenseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LicenseController extends Controller
{
    public function __construct(private readonly LicenseService $service) {}

    public function index(Request $request): Response
    {
        $query = License::with('ownerOrg');

        if ($request->filled('search')) {
            $query->where('name', 'ilike', '%'.$request->search.'%');
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === 'aktif');
        }

        if ($request->filled('owner_org_id')) {
            $query->where('owner_org_id', $request->owner_org_id);
        }

        $licenses = $query->orderBy('is_active', 'desc')->orderBy('expired_at')->paginate(15)->withQueryString();
        $organizations = Organization::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Admin/Assets/Licenses/Index', [
            'licenses' => $licenses,
            'organizations' => $organizations,
            'filters' => $request->only(['search', 'is_active', 'owner_org_id']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Assets/Licenses/Create', [
            ...$this->formData(),
        ]);
    }

    public function store(SaveLicenseRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('admin.licenses.index')
            ->with('success', 'Lisensi berhasil ditambahkan.');
    }

    public function edit(License $license): Response
    {
        return Inertia::render('Admin/Assets/Licenses/Create', [
            'license' => $license->load([
                'location', 'providerOrg', 'ownerOrg', 'ownerEmployee',
                'securityClassification', 'auditLogs.user', 'auditLogs.attachment',
            ]),
            ...$this->formData(),
        ]);
    }

    public function update(SaveLicenseRequest $request, License $license): RedirectResponse
    {
        $this->service->update($license, $request->validated());

        return redirect()->route('admin.licenses.index')
            ->with('success', 'Lisensi berhasil diperbarui.');
    }

    public function destroy(License $license): RedirectResponse
    {
        $this->service->delete($license);

        return redirect()->back()->with('success', 'Lisensi berhasil dihapus.');
    }

    private function formData(): array
    {
        return [
            'organizations' => Organization::orderBy('name')->get(['id', 'name', 'it_contact_name', 'it_contact_phone', 'it_contact_email']),
            'locations' => Location::with('organization')->orderBy('name')->get(['id', 'name', 'organization_id']),
            'employees' => Employee::where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ];
    }
}
