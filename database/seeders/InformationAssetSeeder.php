<?php

namespace Database\Seeders;

use App\Models\Document;
use App\Models\InformationAsset;
use Illuminate\Database\Seeder;

class InformationAssetSeeder extends Seeder
{
    public function run(): void
    {
        $documentIds = Document::pluck('id')->all();

        $formats = ['file_dokumen', 'cetak', 'keduanya'];

        foreach ($formats as $i => $format) {
            InformationAsset::create([
                'document_id' => $documentIds[$i] ?? ($documentIds[0] ?? null),
                'storage_format' => $format,
            ]);
        }
    }
}
