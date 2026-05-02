<?php

namespace Database\Seeders;

use App\Enums\AppStatus;
use App\Enums\AssetStage;
use App\Enums\HttpsStatus;
use App\Models\Location;
use App\Models\Organization;
use App\Models\WebApplication;
use Illuminate\Database\Seeder;

class WebApplicationSeeder extends Seeder
{
    public function run(): void
    {
        $diskominfo = Organization::where('name', 'like', '%Komunikasi dan Informatika%')->first();
        $datacenter = Location::where('name', 'like', '%Data Center%')->first();

        $apps = [
            [
                'name' => 'SIMKEP — Sistem Informasi Manajemen Kepegawaian',
                'description' => 'Aplikasi pengelolaan data kepegawaian daerah Kabupaten Bojonegoro.',
                'stage' => AssetStage::Diterima,
                'app_status' => AppStatus::Aktif,
                'https_status' => HttpsStatus::Aktif,
            ],
            [
                'name' => 'SIAK — Sistem Informasi Administrasi Kependudukan',
                'description' => 'Sistem informasi pengelolaan data kependudukan dan catatan sipil.',
                'stage' => AssetStage::Diterima,
                'app_status' => AppStatus::Aktif,
                'https_status' => HttpsStatus::Aktif,
            ],
            [
                'name' => 'Portal Bojonegoro',
                'description' => 'Website resmi Pemerintah Kabupaten Bojonegoro sebagai media informasi publik.',
                'stage' => AssetStage::Diterima,
                'app_status' => AppStatus::Aktif,
                'https_status' => HttpsStatus::Aktif,
            ],
            [
                'name' => 'SIPPELDA — Sistem Informasi Perencanaan Pembangunan Daerah',
                'description' => 'Sistem untuk perencanaan dan pemantauan pembangunan daerah.',
                'stage' => AssetStage::Diterima,
                'app_status' => AppStatus::Aktif,
                'https_status' => HttpsStatus::Nonaktif,
            ],
            [
                'name' => 'E-Service Diskominfo',
                'description' => 'Portal layanan elektronik Dinas Komunikasi dan Informatika.',
                'stage' => AssetStage::Diterima,
                'app_status' => AppStatus::Idle,
                'https_status' => HttpsStatus::Aktif,
            ],
            [
                'name' => 'SPBE Dashboard',
                'description' => 'Dashboard monitoring Sistem Pemerintahan Berbasis Elektronik Kabupaten Bojonegoro.',
                'stage' => AssetStage::Pengujian,
                'app_status' => AppStatus::Aktif,
                'https_status' => HttpsStatus::Aktif,
            ],
            [
                'name' => 'SiGermas — Sistem Informasi Gerakan Masyarakat Sehat',
                'description' => 'Aplikasi pemantauan program kesehatan masyarakat.',
                'stage' => AssetStage::Revisi,
                'app_status' => AppStatus::Nonaktif,
                'https_status' => HttpsStatus::Nonaktif,
            ],
            [
                'name' => 'E-Procurement Bojonegoro',
                'description' => 'Sistem pengadaan barang dan jasa secara elektronik.',
                'stage' => AssetStage::Diterima,
                'app_status' => AppStatus::Aktif,
                'https_status' => HttpsStatus::Aktif,
            ],
            [
                'name' => 'SiRencana — Sistem Informasi Rencana Kerja',
                'description' => 'Aplikasi pengelolaan rencana kerja OPD.',
                'stage' => AssetStage::Persiapan,
                'app_status' => AppStatus::Idle,
                'https_status' => HttpsStatus::Nonaktif,
            ],
            [
                'name' => 'SIM Naker — Sistem Informasi Manajemen Ketenagakerjaan',
                'description' => 'Sistem informasi pengelolaan data ketenagakerjaan daerah.',
                'stage' => AssetStage::Draft,
                'app_status' => AppStatus::Nonaktif,
                'https_status' => HttpsStatus::Nonaktif,
            ],
        ];

        foreach ($apps as $data) {
            WebApplication::firstOrCreate(
                ['name' => $data['name']],
                [
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'stage' => $data['stage'],
                    'app_status' => $data['app_status'],
                    'https_status' => $data['https_status'],
                    'owner_org_id' => $diskominfo?->id,
                    'provider_org_id' => $diskominfo?->id,
                    'location_id' => $datacenter?->id,
                    'owner_contact_type' => 'auto',
                ]
            );
        }
    }
}
