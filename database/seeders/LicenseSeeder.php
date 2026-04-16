<?php

namespace Database\Seeders;

use App\Models\License;
use App\Models\Location;
use App\Models\Organization;
use Illuminate\Database\Seeder;

class LicenseSeeder extends Seeder
{
    public function run(): void
    {
        $diskominfo = Organization::where('name', 'like', '%Komunikasi dan Informatika%')->first();
        $datacenter = Location::where('name', 'like', '%Data Center%')->first();

        $licenses = [
            [
                'name' => 'Windows Server 2022 Datacenter',
                'description' => 'Lisensi sistem operasi server untuk virtualisasi dan cloud hybrid.',
                'version' => '2022',
                'expired_at' => '2026-12-31',
                'is_active' => true,
            ],
            [
                'name' => 'Oracle Database Enterprise Edition',
                'description' => 'Lisensi basis data enterprise untuk sistem kritis.',
                'version' => '19c',
                'expired_at' => '2025-06-30',
                'is_active' => true,
            ],
            [
                'name' => 'Microsoft 365 Business Premium',
                'description' => 'Paket produktivitas Office 365 dengan keamanan tingkat enterprise.',
                'version' => '2024',
                'expired_at' => '2025-03-31',
                'is_active' => true,
            ],
            [
                'name' => 'VMware vSphere Enterprise Plus',
                'description' => 'Platform virtualisasi untuk manajemen server terpusat.',
                'version' => '8.0',
                'expired_at' => '2027-09-30',
                'is_active' => true,
            ],
            [
                'name' => 'Trend Micro Deep Security',
                'description' => 'Keamanan komprehensif untuk server fisik, virtual, dan cloud.',
                'version' => '20.0',
                'expired_at' => '2025-12-31',
                'is_active' => true,
            ],
            [
                'name' => 'Adobe Creative Cloud for Teams',
                'description' => 'Paket aplikasi desain grafis dan multimedia.',
                'version' => '2024',
                'expired_at' => '2024-08-31',
                'is_active' => false,
            ],
            [
                'name' => 'Cisco Meraki Network License',
                'description' => 'Lisensi manajemen jaringan berbasis cloud.',
                'version' => 'Enterprise',
                'expired_at' => '2026-06-30',
                'is_active' => true,
            ],
            [
                'name' => 'SQL Server 2022 Standard',
                'description' => 'Lisensi basis data SQL Server untuk aplikasi bisnis.',
                'version' => '2022',
                'expired_at' => '2028-10-14',
                'is_active' => true,
            ],
            [
                'name' => 'Kaspersky Endpoint Security for Business',
                'description' => 'Antivirus dan keamanan endpoint untuk perangkat organisasi.',
                'version' => '11.11',
                'expired_at' => '2025-01-15',
                'is_active' => false,
            ],
            [
                'name' => 'AutoCAD LT 2024',
                'description' => 'Lisensi perangkat lunak gambar teknik 2D.',
                'version' => '2024',
                'expired_at' => '2025-09-30',
                'is_active' => true,
            ],
        ];

        foreach ($licenses as $data) {
            License::firstOrCreate(
                ['name' => $data['name']],
                [
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'version' => $data['version'],
                    'expired_at' => $data['expired_at'],
                    'is_active' => $data['is_active'],
                    'owner_org_id' => $diskominfo?->id,
                    'provider_org_id' => $diskominfo?->id,
                    'location_id' => $datacenter?->id,
                    'owner_contact_type' => 'auto',
                ]
            );
        }
    }
}
