<?php

namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    public function run(): void
    {
        $organizations = [
            [
                'name' => 'Dinas Komunikasi dan Informatika',
                'it_contact_name' => 'Bidang Teknologi Informasi dan Komunikasi',
                'it_contact_phone' => '(0353) 123456',
                'it_contact_email' => 'diskominfo@bojonegorokab.go.id',
            ],
            [
                'name' => 'Badan Pengelola Keuangan dan Aset Daerah',
                'it_contact_name' => null,
                'it_contact_phone' => null,
                'it_contact_email' => null,
            ],
            [
                'name' => 'Dinas Perhubungan',
                'it_contact_name' => null,
                'it_contact_phone' => null,
                'it_contact_email' => null,
            ],
            [
                'name' => 'Dinas Kesehatan',
                'it_contact_name' => null,
                'it_contact_phone' => null,
                'it_contact_email' => null,
            ],
            [
                'name' => 'Dinas Kependudukan dan Pencatatan Sipil',
                'it_contact_name' => null,
                'it_contact_phone' => null,
                'it_contact_email' => null,
            ],
        ];

        foreach ($organizations as $data) {
            Organization::firstOrCreate(['name' => $data['name']], $data);
        }
    }
}
