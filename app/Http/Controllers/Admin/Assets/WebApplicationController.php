<?php

// Tujuan: CRUD aplikasi web beserta sinkronisasi jaringan, tech stack, VM, dan keamanan
// Caller: routes/web.php admin group
// Side Effects: DB write

namespace App\Http\Controllers\Admin\Assets;

use App\Enums\AppStatus;
use App\Enums\AssetStage;
use App\Enums\HttpsStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Assets\SaveWebApplicationRequest;
use App\Models\Employee;
use App\Models\IpAddress;
use App\Models\Location;
use App\Models\Organization;
use App\Models\Subdomain;
use App\Models\TechStack;
use App\Models\TechStackCategory;
use App\Models\Vendor;
use App\Models\VirtualAssetGuide;
use App\Models\WebApplication;
use App\Services\Assets\WebApplicationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class WebApplicationController extends Controller
{
    public function __construct(private readonly WebApplicationService $service) {}

    public function index(Request $request): Response
    {
        $filters = $request->only(['search', 'stage', 'app_status', 'https_status', 'owner_org_id']);
        $webApplications = $this->service->indexQuery($filters)->paginate(15)->withQueryString();
        $organizations = Organization::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Admin/Assets/WebApplications/Index', [
            'webApplications' => $webApplications,
            'organizations' => $organizations,
            'stageOptions' => collect(AssetStage::cases())->map(fn ($e) => ['name' => $e->label(), 'value' => $e->value])->values()->all(),
            'appStatusOptions' => collect(AppStatus::cases())->map(fn ($e) => ['name' => $e->label(), 'value' => $e->value])->values()->all(),
            'httpsStatusOptions' => collect(HttpsStatus::cases())->map(fn ($e) => ['name' => $e->label(), 'value' => $e->value])->values()->all(),
            'filters' => $request->only(['search', 'stage', 'app_status', 'https_status', 'owner_org_id']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Assets/WebApplications/Create', [
            ...$this->formData(),
            'guides' => $this->guidesData(null),
        ]);
    }

    public function store(SaveWebApplicationRequest $request): RedirectResponse
    {
        $webApplication = $this->service->create($request->validated());

        return redirect()->route('admin.web-applications.show', $webApplication)
            ->with('success', 'Aplikasi web berhasil ditambahkan.');
    }

    public function show(WebApplication $webApplication): Response
    {
        $webApplication->load([
            'location', 'providerOrg', 'ownerOrg', 'ownerEmployee',
            'vendor', 'vms', 'networks.ipAddress', 'networks.subdomain',
            'techStacks.techStack.category',
            'securityClassification',
            'securityNotes.user', 'securityNotes.attachment',
            'auditLogs.user', 'auditLogs.attachment',
            'incidents.incidentType',
        ]);

        return Inertia::render('Admin/Assets/WebApplications/Show', [
            'webApplication' => $webApplication,
            'guides' => $this->guidesData($webApplication),
        ]);
    }

    public function edit(WebApplication $webApplication): Response
    {
        $webApplication->load([
            'location', 'providerOrg', 'ownerOrg', 'ownerEmployee',
            'vendor', 'vms', 'networks.ipAddress', 'networks.subdomain',
            'techStacks.techStack',
            'securityClassification',
            'securityNotes.user', 'securityNotes.attachment',
            'auditLogs.user', 'auditLogs.attachment',
            'incidents.incidentType',
        ]);

        return Inertia::render('Admin/Assets/WebApplications/Create', [
            'webApplication' => $webApplication,
            ...$this->formData(),
            'guides' => $this->guidesData($webApplication),
        ]);
    }

    public function update(SaveWebApplicationRequest $request, WebApplication $webApplication): RedirectResponse
    {
        $this->service->update($webApplication, $request->validated());

        return redirect()->route('admin.web-applications.show', $webApplication)
            ->with('success', 'Aplikasi web berhasil diperbarui.');
    }

    public function destroy(WebApplication $webApplication): RedirectResponse
    {
        $this->service->delete($webApplication);

        return redirect()->back()->with('success', 'Aplikasi web berhasil dihapus.');
    }

    public function export()
    {
        /** @var Collection<int, WebApplication> $data */
        $data = $this->service->indexQuery([])->with([
            'ownerOrg',
            'networks' => fn ($q) => $q->where('is_primary', true)->with(['ipAddress', 'subdomain']),
        ])->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="aplikasi-web_'.now()->format('Y-m-d').'.csv"',
        ];

        return response()->stream(function () use ($data) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8

            fputcsv($out, [
                'Nama Aplikasi', 'Pemilik', 'Tahap',
                'Status Aplikasi', 'Status HTTPS',
                'Domain / Subdomain', 'IP Private', 'IP Public',
            ]);

            foreach ($data as $item) {
                $net = $item->networks->first();
                fputcsv($out, [
                    $item->name,
                    $item->ownerOrg?->name,
                    $item->stage instanceof \BackedEnum ? $item->stage->value : $item->stage,
                    $item->app_status instanceof \BackedEnum ? $item->app_status->value : $item->app_status,
                    $item->https_status instanceof \BackedEnum ? $item->https_status->value : $item->https_status,
                    $net?->subdomain?->subdomain,
                    $net?->ipAddress?->private_ip,
                    $net?->ipAddress?->public_ip,
                ]);
            }

            fclose($out);
        }, 200, $headers);
    }

    private function guidesData(?WebApplication $asset): array
    {
        $guides = VirtualAssetGuide::with(['guideAttachments.document.officialAttachment'])
            ->where('type', 'web')
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
            'ipAddresses' => IpAddress::orderBy('private_ip')->get(['id', 'private_ip', 'public_ip', 'description']),
            'subdomains' => Subdomain::orderBy('subdomain')->get(['id', 'subdomain', 'description']),
            'stageOptions' => collect(AssetStage::cases())->map(fn ($e) => ['name' => $e->label(), 'value' => $e->value])->values()->all(),
            'appStatusOptions' => collect(AppStatus::cases())->map(fn ($e) => ['name' => $e->label(), 'value' => $e->value])->values()->all(),
            'httpsStatusOptions' => collect(HttpsStatus::cases())->map(fn ($e) => ['name' => $e->label(), 'value' => $e->value])->values()->all(),
        ];
    }
}
