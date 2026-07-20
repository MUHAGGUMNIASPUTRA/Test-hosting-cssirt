<?php

namespace App\Services\Assets;

use App\Models\Organization;
use App\Models\WebApplication;
use App\Models\WebAppNetwork;
use App\Models\WebAppTechStack;
use App\Models\WebAppVm;
use Illuminate\Database\Eloquent\Builder;

class WebApplicationService
{
    public function indexQuery(array $filters): Builder
    {
        $query = WebApplication::with([
            'location',
            'ownerOrg',
            'networks' => fn ($q) => $q->where('is_primary', true)->with(['ipAddress', 'subdomain']),
        ]);
        // Catatan: .latest() dihapus karena digantikan oleh ordering di bawah

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                    ->orWhereHas('networks', fn ($nq) => $nq
                        ->whereHas('ipAddress', fn ($iq) => $iq
                            ->where('private_ip', 'ilike', "%{$search}%")
                            ->orWhere('public_ip', 'ilike', "%{$search}%")
                        )
                        ->orWhereHas('subdomain', fn ($sq) => $sq
                            ->where('subdomain', 'ilike', "%{$search}%")
                        )
                    );
            });
        }

        if (! empty($filters['stage'])) {
            $query->where('stage', $filters['stage']);
        }
        if (! empty($filters['app_status'])) {
            $query->where('app_status', $filters['app_status']);
        }
        if (! empty($filters['https_status'])) {
            $query->where('https_status', $filters['https_status']);
        }
        if (! empty($filters['owner_org_id'])) {
            $query->where('owner_org_id', $filters['owner_org_id']);
        }

        // Urut: status aplikasi (aktif → idle → nonaktif) → nama pemilik → nama aplikasi
        $query
            ->orderByRaw("
            CASE app_status
                WHEN 'aktif'    THEN 1
                WHEN 'idle'     THEN 2
                WHEN 'nonaktif' THEN 3
                ELSE 4
            END
        ")
            ->orderBy(
                Organization::select('name')
                    ->whereColumn('organizations.id', 'web_applications.owner_org_id')
                    ->limit(1)
            )
            ->orderBy('web_applications.name');

        return $query;
    }

    public function create(array $data): WebApplication
    {
        $app = WebApplication::create($this->mainFields($data));

        $this->syncVms($app, $data['vms'] ?? []);
        $this->syncNetworks($app, $data['networks'] ?? []);
        $this->syncTechStacks($app, $data['tech_stacks'] ?? []);
        $this->syncSecurity($app, $data['security'] ?? []);

        return $app;
    }

    public function update(WebApplication $app, array $data): void
    {
        $app->update($this->mainFields($data));

        $this->syncVms($app, $data['vms'] ?? []);
        $this->syncNetworks($app, $data['networks'] ?? []);
        $this->syncTechStacks($app, $data['tech_stacks'] ?? []);
        $this->syncSecurity($app, $data['security'] ?? []);
    }

    public function delete(WebApplication $app): void
    {
        $app->delete();
    }

    private function mainFields(array $data): array
    {
        return array_intersect_key($data, array_flip([
            'name', 'description', 'stage', 'app_status', 'https_status',
            'location_id', 'provider_org_id', 'owner_org_id',
            'owner_contact_type', 'owner_employee_id', 'vendor_id',
        ]));
    }

    private function syncVms(WebApplication $app, array $vms): void
    {
        $app->vms()->delete();
        foreach ($vms as $index => $vm) {
            WebAppVm::create([
                'web_application_id' => $app->id,
                'processor' => $vm['processor'] ?? null,
                'ram' => $vm['ram'] ?? null,
                'hdd' => $vm['hdd'] ?? null,
                'sort_order' => $index,
            ]);
        }
    }

    private function syncNetworks(WebApplication $app, array $networks): void
    {
        $app->networks()->delete();
        foreach ($networks as $index => $network) {
            WebAppNetwork::create([
                'web_application_id' => $app->id,
                'environment' => $network['environment'] ?? null,
                'ip_address_id' => $network['ip_address_id'] ?? null,
                'subdomain_id' => $network['subdomain_id'] ?? null,
                'is_primary' => $index === 0,
                'sort_order' => $index,
            ]);
        }
    }

    private function syncTechStacks(WebApplication $app, array $techStacks): void
    {
        $app->techStacks()->delete();
        foreach ($techStacks as $stack) {
            WebAppTechStack::create([
                'web_application_id' => $app->id,
                'tech_stack_id' => $stack['tech_stack_id'],
                'version' => $stack['version'] ?? null,
            ]);
        }
    }

    private function syncSecurity(WebApplication $app, array $security): void
    {
        if (empty($security)) {
            return;
        }

        $app->securityClassification()->updateOrCreate(
            ['asset_type' => WebApplication::class, 'asset_id' => $app->id],
            [
                'confidentiality' => $security['confidentiality'] ?? 1,
                'integrity' => $security['integrity'] ?? 1,
                'availability' => $security['availability'] ?? 1,
                'notes' => $security['notes'] ?? null,
            ]
        );
    }
}
