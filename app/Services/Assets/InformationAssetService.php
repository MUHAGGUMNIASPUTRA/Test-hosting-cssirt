<?php

// Tujuan: Service CRUD aset informasi beserta sinkronisasi klasifikasi keamanan
// Caller: InformationAssetController
// Side Effects: DB write

namespace App\Services\Assets;

use App\Models\InformationAsset;

class InformationAssetService
{
    public function create(array $data): InformationAsset
    {
        $asset = InformationAsset::create($this->mainFields($data));

        $this->syncSecurity($asset, $data['security'] ?? []);

        return $asset;
    }

    public function update(InformationAsset $asset, array $data): void
    {
        $asset->update($this->mainFields($data));

        $this->syncSecurity($asset, $data['security'] ?? []);
    }

    public function delete(InformationAsset $asset): void
    {
        $asset->delete();
    }

    private function mainFields(array $data): array
    {
        return array_intersect_key($data, array_flip([
            'document_id', 'storage_format', 'location_id', 'owner_org_id',
        ]));
    }

    private function syncSecurity(InformationAsset $asset, array $security): void
    {
        if (empty($security)) {
            return;
        }

        $asset->securityClassification()->updateOrCreate(
            ['asset_type' => InformationAsset::class, 'asset_id' => $asset->id],
            [
                'confidentiality' => $security['confidentiality'] ?? 1,
                'integrity' => $security['integrity'] ?? 1,
                'availability' => $security['availability'] ?? 1,
                'notes' => $security['notes'] ?? null,
            ]
        );
    }
}
