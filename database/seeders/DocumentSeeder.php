<?php

namespace Database\Seeders;

use App\Enums\DocumentStage;
use App\Models\Document;
use App\Models\DocumentArea;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DocumentSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $areaIds = DocumentArea::pluck('id')->toArray();
        $stages = DocumentStage::values();

        $versions = ['1.0', '1.1', '1.2', '2.0', '2.1', '3.0', null];
        $referenceFormats = [
            fn ($i) => "No. {$i}/CSIRT/BPN/{$faker->year()}",
            fn ($i) => "SK-{$faker->year()}-".str_pad($i, 3, '0', STR_PAD_LEFT),
            fn ($i) => "REF/{$faker->year()}/".str_pad($i, 4, '0', STR_PAD_LEFT),
            fn () => null,
        ];

        for ($i = 1; $i <= 30; $i++) {
            $title = rtrim($faker->sentence(rand(3, 6)), '.');
            $hasOfficialFile = $faker->boolean(70);
            $hasDraftLink = $faker->boolean(40);
            $areaId = ! empty($areaIds) && $faker->boolean(80)
                ? $faker->randomElement($areaIds)
                : null;

            $refFn = $faker->randomElement($referenceFormats);
            $referenceNumber = $refFn($i);

            Document::create([
                'title' => $title,
                'slug' => Str::slug($title).'-'.Str::random(4),
                'description' => $faker->boolean(75) ? $faker->paragraph(rand(1, 3)) : null,
                'draft_file_path' => $hasDraftLink
                    ? 'https://docs.google.com/document/d/'.$faker->uuid()
                    : null,
                'official_file_path' => $hasOfficialFile
                    ? 'https://drive.google.com/file/d/'.$faker->uuid().'/view'
                    : null,
                'reference_number' => $referenceNumber,
                'stage' => $faker->randomElement([...$stages, null]),
                'version' => $faker->randomElement($versions),
                'published_at' => $faker->boolean(70)
                    ? $faker->dateTimeBetween('-2 years', 'now')
                    : null,
                'is_public' => $faker->boolean(60),
                'document_area_id' => $areaId,
            ]);
        }
    }
}
