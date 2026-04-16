<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Organization;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $diskominfo = Organization::where('name', 'like', '%Komunikasi dan Informatika%')->first();

        if (! $diskominfo) {
            return;
        }

        $departments = [
            'Pimpinan',
            'Bidang Teknologi Informasi dan Komunikasi',
            'Bidang Pengelolaan Informasi dan Komunikasi Publik',
            'Bidang E-Government',
            'Sekretariat',
            'UPT LPSE',
        ];

        foreach ($departments as $name) {
            Department::firstOrCreate(
                ['name' => $name, 'organization_id' => $diskominfo->id],
            );
        }
    }
}
