<?php

namespace App\Http\Controllers\Admin\Assets;

use App\Enums\AppStatus;
use App\Enums\AssetStage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Assets\SaveMobileApplicationRequest;
use App\Models\Employee;
use App\Models\Location;
use App\Models\MobileApplication;
use App\Models\Organization;
use App\Models\TechStack;
use App\Models\Vendor;
use App\Models\VirtualAssetGuide;
use App\Services\Assets\MobileApplicationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MobileApplicationController extends Controller
{
    public function __construct(private readonly MobileApplicationService $service) {}

    public function index(Request $request): Response
    {
        $query = MobileApplication::with('ownerOrg')->latest();

        if ($request->filled('search')) {
            $query->where('name', 'ilike', '%'.$request->search.'%');
        }

        if ($request->filled('stage')) {
            $query->where('stage', $request->stage);
        }

        if ($request->filled('app_status')) {
            $query->where('app_status', $request->app_status);
        }

        if ($request->filled('owner_org_id')) {
            $query->where('owner_org_id', $request->owner_org_id);
        }

        $mobileApplications = $query->paginate(15)->withQueryString();
        $organizations = Organization::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Admin/Assets/MobileApplications/Index', [
            'mobileApplications' => $mobileApplications,
            'organizations' => $organizations,
            'stageOptions' => AssetStage::cases(),
            'appStatusOptions' => AppStatus::cases(),
            'filters' => $request->only(['search', 'stage', 'app_status', 'owner_org_id']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Assets/MobileApplications/Create', [
            ...$this->formData(),
        ]);
    }

    public function store(SaveMobileApplicationRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('admin.mobile-applications.index')
            ->with('success', 'Aplikasi mobile berhasil ditambahkan.');
    }

    public function edit(MobileApplication $mobileApplication): Response
    {
        return Inertia::render('Admin/Assets/MobileApplications/Create', [
            'mobileApplication' => $mobileApplication->load([
                'location', 'providerOrg', 'ownerOrg', 'ownerEmployee',
                'vendor', 'techStacks.techStack',
                'securityClassification', 'auditLogs.user', 'auditLogs.attachment',
            ]),
            ...$this->formData(),
        ]);
    }

    public function update(SaveMobileApplicationRequest $request, MobileApplication $mobileApplication): RedirectResponse
    {
        $this->service->update($mobileApplication, $request->validated());

        return redirect()->route('admin.mobile-applications.index')
            ->with('success', 'Aplikasi mobile berhasil diperbarui.');
    }

    public function destroy(MobileApplication $mobileApplication): RedirectResponse
    {
        $this->service->delete($mobileApplication);

        return redirect()->back()->with('success', 'Aplikasi mobile berhasil dihapus.');
    }

    private function formData(): array
    {
        return [
            'organizations' => Organization::orderBy('name')->get(['id', 'name', 'it_contact_name', 'it_contact_phone', 'it_contact_email']),
            'locations' => Location::with('organization')->orderBy('name')->get(['id', 'name', 'organization_id']),
            'vendors' => Vendor::orderBy('company_name')->get(['id', 'company_name']),
            'employees' => Employee::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'techStacks' => TechStack::with('category')->orderBy('name')->get(['id', 'name', 'category_id']),
            'guides' => VirtualAssetGuide::where('type', 'mobile')->orderBy('name')->get(['id', 'name']),
            'stageOptions' => AssetStage::cases(),
            'appStatusOptions' => AppStatus::cases(),
        ];
    }
}
