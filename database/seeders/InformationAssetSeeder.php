<?php

namespace Database\Seeders;

use App\Models\InformationAsset;
use Illuminate\Database\Seeder;

class InformationAssetSeeder extends Seeder
{
    public function run(): void
    {
        $assets = [
            [
                'document_id' => null,
                'storage_format' => 'file_dokumen',
            ],
            [
                'document_id' => null,
                'storage_format' => 'cetak',
            ],
            [
                'document_id' => null,
                'storage_format' => 'keduanya',
            ],
        ];

        foreach ($assets as $asset) {
            InformationAsset::create($asset);
        }
    }
}
