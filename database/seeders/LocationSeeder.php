<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Organization;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $diskominfo = Organization::where('name', 'like', '%Komunikasi dan Informatika%')->first();

        if (! $diskominfo) {
            return;
        }

        $locations = [
            'Data Center Diskominfo',
            'Ruang Server Bidang TIK',
            'Bidang Pengelolaan Informasi dan Komunikasi Publik',
            'Bidang E-Government',
            'Sekretariat Diskominfo',
        ];

        foreach ($locations as $name) {
            Location::firstOrCreate(
                ['name' => $name, 'organization_id' => $diskominfo->id],
            );
        }
    }
}
