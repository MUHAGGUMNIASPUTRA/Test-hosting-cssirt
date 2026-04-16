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
use App\Models\TechStackCategory;
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
            'stageOptions' => collect(AssetStage::cases())->map(fn ($e) => ['name' => $e->label(), 'value' => $e->value])->values()->all(),
            'appStatusOptions' => collect(AppStatus::cases())->map(fn ($e) => ['name' => $e->label(), 'value' => $e->value])->values()->all(),
            'filters' => $request->only(['search', 'stage', 'app_status', 'owner_org_id']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Assets/MobileApplications/Create', [
            ...$this->formData(),
            'guides' => $this->guidesData(null),
        ]);
    }

    public function store(SaveMobileApplicationRequest $request): RedirectResponse
    {
        $mobileApplication = $this->service->create($request->validated());

        return redirect()->route('admin.mobile-applications.show', $mobileApplication)
            ->with('success', 'Aplikasi mobile berhasil ditambahkan.');
    }

    public function show(MobileApplication $mobileApplication): Response
    {
        $mobileApplication->load([
            'location', 'providerOrg', 'ownerOrg', 'ownerEmployee',
            'vendor', 'techStacks.techStack.category',
            'securityClassification',
            'securityNotes.user', 'securityNotes.attachment',
            'auditLogs.user', 'auditLogs.attachment',
        ]);

        return Inertia::render('Admin/Assets/MobileApplications/Show', [
            'mobileApplication' => $mobileApplication,
            'guides' => $this->guidesData($mobileApplication),
        ]);
    }

    public function edit(MobileApplication $mobileApplication): Response
    {
        $mobileApplication->load([
            'location', 'providerOrg', 'ownerOrg', 'ownerEmployee',
            'vendor', 'techStacks.techStack',
            'securityClassification',
            'securityNotes.user', 'securityNotes.attachment',
            'auditLogs.user', 'auditLogs.attachment',
        ]);

        return Inertia::render('Admin/Assets/MobileApplications/Create', [
            'mobileApplication' => $mobileApplication,
            ...$this->formData(),
            'guides' => $this->guidesData($mobileApplication),
        ]);
    }

    public function update(SaveMobileApplicationRequest $request, MobileApplication $mobileApplication): RedirectResponse
    {
        $this->service->update($mobileApplication, $request->validated());

        return redirect()->route('admin.mobile-applications.show', $mobileApplication)
            ->with('success', 'Aplikasi mobile berhasil diperbarui.');
    }

    public function destroy(MobileApplication $mobileApplication): RedirectResponse
    {
        $this->service->delete($mobileApplication);

        return redirect()->back()->with('success', 'Aplikasi mobile berhasil dihapus.');
    }

    private function guidesData(?MobileApplication $asset): array
    {
        $guides = VirtualAssetGuide::with(['guideAttachments.attachment'])
            ->where('type', 'mobile')
            ->orderBy('name')
            ->get(['id', 'name', 'description']);

        if ($asset === null) {
            return $guides->map(fn ($g) => array_merge($g->toArray(), ['acknowledged' => false]))->values()->all();
        }

        $acknowledgedIds = $asset->guideAcknowledgements()->pluck('guide_id')->all();

        return $guides->map(fn ($g) => array_merge($g->toArray(), [
            'acknowledged' => in_array($g->id, $acknowledgedIds),
        ]))->values()->all();
    }

    private function formData(): array
    {
        return [
            'organizations' => Organization::orderBy('name')->get(['id', 'name', 'it_contact_name', 'it_contact_phone', 'it_contact_email']),
            'locations' => Location::with('organization')->orderBy('name')->get(['id', 'name', 'organization_id']),
            'vendors' => Vendor::orderBy('company_name')->get(['id', 'company_name']),
            'employees' => Employee::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'techStacks' => TechStack::with('category')->orderBy('name')->get(['id', 'name', 'category_id']),
            'techStackCategories' => TechStackCategory::orderBy('name')->get(['id', 'name']),
            'stageOptions' => collect(AssetStage::cases())->map(fn ($e) => ['name' => $e->label(), 'value' => $e->value])->values()->all(),
            'appStatusOptions' => collect(AppStatus::cases())->map(fn ($e) => ['name' => $e->label(), 'value' => $e->value])->values()->all(),
        ];
    }
}
