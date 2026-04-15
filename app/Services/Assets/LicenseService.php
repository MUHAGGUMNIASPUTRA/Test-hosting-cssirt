<?php

namespace App\Services\Assets;

use App\Models\License;

class LicenseService
{
    public function create(array $data): License
    {
        $license = License::create($this->mainFields($data));

        $this->syncSecurity($license, $data['security'] ?? []);

        return $license;
    }

    public function update(License $license, array $data): void
    {
        $license->update($this->mainFields($data));

        $this->syncSecurity($license, $data['security'] ?? []);
    }

    public function delete(License $license): void
    {
        $license->delete();
    }

    private function mainFields(array $data): array
    {
        return array_intersect_key($data, array_flip([
            'name', 'description', 'is_active', 'version', 'expired_at',
            'location_id', 'provider_org_id', 'owner_org_id',
            'owner_contact_type', 'owner_employee_id',
        ]));
    }

    private function syncSecurity(License $license, array $security): void
    {
        if (empty($security)) {
            return;
        }

        $license->securityClassification()->updateOrCreate(
            ['asset_type' => License::class, 'asset_id' => $license->id],
            [
                'confidentiality' => $security['confidentiality'] ?? 1,
                'integrity' => $security['integrity'] ?? 1,
                'availability' => $security['availability'] ?? 1,
                'notes' => $security['notes'] ?? null,
            ]
        );
    }
}
