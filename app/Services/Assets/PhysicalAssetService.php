<?php

// Tujuan: Service CRUD aset fisik, termasuk pengelolaan attachment via AttachmentService
// Caller: PhysicalAssetController
// Side Effects: DB write, storage I/O (attachment)

namespace App\Services\Assets;

use App\Models\PhysicalAsset;
use App\Services\AttachmentService;
use Illuminate\Http\UploadedFile;

class PhysicalAssetService
{
    public function __construct(private readonly AttachmentService $attachmentService) {}

    public function create(array $data, ?UploadedFile $file): PhysicalAsset
    {
        $attachment = $this->attachmentService->resolve(
            $file,
            $data['attachment_type'] ?? null,
            $data['attachment_link'] ?? null,
            null,
            'public',
            'physical-assets',
        );

        return PhysicalAsset::create([
            ...$this->mainFields($data),
            'attachment_id' => $attachment?->id,
        ]);
    }

    public function update(PhysicalAsset $asset, array $data, ?UploadedFile $file): void
    {
        $attachment = $this->attachmentService->resolve(
            $file,
            $data['attachment_type'] ?? null,
            $data['attachment_link'] ?? null,
            $asset->attachment,
            'public',
            'physical-assets',
        );

        $asset->update([
            ...$this->mainFields($data),
            'attachment_id' => $attachment?->id,
        ]);
    }

    public function delete(PhysicalAsset $asset): void
    {
        if ($asset->attachment) {
            $this->attachmentService->delete($asset->attachment);
        }

        $asset->delete();
    }

    private function mainFields(array $data): array
    {
        return array_intersect_key($data, array_flip([
            'asset_code', 'name', 'description', 'specifications', 'year',
            'location_id', 'owner_org_id', 'owner_contact_type', 'owner_employee_id',
        ]));
    }
}
