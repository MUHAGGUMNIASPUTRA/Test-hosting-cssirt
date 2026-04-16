<?php

namespace Database\Seeders;

use App\Enums\AppStatus;
use App\Enums\AssetStage;
use App\Models\MobileApplication;
use App\Models\Organization;
use Illuminate\Database\Seeder;

class MobileApplicationSeeder extends Seeder
{
    public function run(): void
    {
        $diskominfo = Organization::where('name', 'like', '%Komunikasi dan Informatika%')->first();

        $apps = [
            [
                'name' => 'BojonegoroAku',
                'description' => 'Aplikasi informasi dan layanan publik Kabupaten Bojonegoro.',
                'stage' => AssetStage::Diterima,
                'app_status' => AppStatus::Aktif,
                'current_version' => '2.3.1',
            ],
            [
                'name' => 'SiCekMatang',
                'description' => 'Aplikasi monitoring dan pelaporan kondisi jalan daerah.',
                'stage' => AssetStage::Diterima,
                'app_status' => AppStatus::Aktif,
                'current_version' => '1.5.0',
            ],
            [
                'name' => 'SiPapeling',
                'description' => 'Sistem Pelayanan Pengaduan Keliling — aplikasi pelaporan aduan masyarakat.',
                'stage' => AssetStage::Diterima,
                'app_status' => AppStatus::Aktif,
                'current_version' => '3.0.2',
            ],
            [
                'name' => 'Go-Ngoro',
                'description' => 'Aplikasi transportasi dan mobilitas warga Bojonegoro.',
                'stage' => AssetStage::Pengujian,
                'app_status' => AppStatus::Idle,
                'current_version' => '0.9.5',
            ],
            [
                'name' => 'SIMANTAP',
                'description' => 'Sistem Informasi Manajemen Anggaran Terpadu.',
                'stage' => AssetStage::Diterima,
                'app_status' => AppStatus::Aktif,
                'current_version' => '2.1.0',
            ],
            [
                'name' => 'E-Lapor Bojonegoro',
                'description' => 'Aplikasi pelaporan dan pengaduan masyarakat secara digital.',
                'stage' => AssetStage::Diterima,
                'app_status' => AppStatus::Aktif,
                'current_version' => '1.8.3',
            ],
            [
                'name' => 'InfoBojonegoro',
                'description' => 'Aplikasi berita dan informasi daerah Kabupaten Bojonegoro.',
                'stage' => AssetStage::Revisi,
                'app_status' => AppStatus::Nonaktif,
                'current_version' => '1.2.0',
            ],
            [
                'name' => 'Lapor OPD',
                'description' => 'Aplikasi pelaporan kinerja Organisasi Perangkat Daerah.',
                'stage' => AssetStage::Persiapan,
                'app_status' => AppStatus::Idle,
                'current_version' => '0.8.0',
            ],
        ];

        foreach ($apps as $data) {
            MobileApplication::firstOrCreate(
                ['name' => $data['name']],
                [
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'stage' => $data['stage'],
                    'app_status' => $data['app_status'],
                    'current_version' => $data['current_version'],
                    'owner_org_id' => $diskominfo?->id,
                    'provider_org_id' => $diskominfo?->id,
                    'owner_contact_type' => 'auto',
                ]
            );
        }
    }
}
