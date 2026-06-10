<?php

namespace App\Services\Assets;

use App\Models\MobileApplication;
use App\Models\MobileAppTechStack;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Builder;

class MobileApplicationService
{
    public function indexQuery(array $filters): Builder
    {
        $query = MobileApplication::with('ownerOrg');
        // Catatan: .latest() dihapus karena digantikan oleh ordering di bawah

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                    ->orWhere('app_link', 'ilike', "%{$search}%");
            });
        }

        if (! empty($filters['stage'])) {
            $query->where('stage', $filters['stage']);
        }
        if (! empty($filters['app_status'])) {
            $query->where('app_status', $filters['app_status']);
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
                    ->whereColumn('organizations.id', 'mobile_applications.owner_org_id')
                    ->limit(1)
            )
            ->orderBy('mobile_applications.name');

        return $query;
    }

    public function create(array $data): MobileApplication
    {
        $app = MobileApplication::create($this->mainFields($data));

        $this->syncTechStacks($app, $data['tech_stacks'] ?? []);
        $this->syncSecurity($app, $data['security'] ?? []);

        return $app;
    }

    public function update(MobileApplication $app, array $data): void
    {
        $app->update($this->mainFields($data));

        $this->syncTechStacks($app, $data['tech_stacks'] ?? []);
        $this->syncSecurity($app, $data['security'] ?? []);
    }

    public function delete(MobileApplication $app): void
    {
        $app->delete();
    }

    private function mainFields(array $data): array
    {
        return array_intersect_key($data, array_flip([
            'name', 'description', 'stage', 'app_status', 'current_version',
            'app_link', 'location_id', 'provider_org_id', 'owner_org_id',
            'owner_contact_type', 'owner_employee_id', 'vendor_id',
        ]));
    }

    private function syncTechStacks(MobileApplication $app, array $techStacks): void
    {
        $app->techStacks()->delete();
        foreach ($techStacks as $stack) {
            MobileAppTechStack::create([
                'mobile_application_id' => $app->id,
                'tech_stack_id' => $stack['tech_stack_id'],
                'version' => $stack['version'] ?? null,
            ]);
        }
    }

    private function syncSecurity(MobileApplication $app, array $security): void
    {
        if (empty($security)) {
            return;
        }

        $app->securityClassification()->updateOrCreate(
            ['asset_type' => MobileApplication::class, 'asset_id' => $app->id],
            [
                'confidentiality' => $security['confidentiality'] ?? 1,
                'integrity' => $security['integrity'] ?? 1,
                'availability' => $security['availability'] ?? 1,
                'notes' => $security['notes'] ?? null,
            ]
        );
    }
}
