<?php

namespace Database\Seeders;

use App\Enums\VirtualGuideType;
use App\Models\VirtualAssetGuide;
use Illuminate\Database\Seeder;

class VirtualAssetGuideSeeder extends Seeder
{
    public function run(): void
    {
        $guides = [
            [
                'name' => 'Panduan Pengembangan Aplikasi Web',
                'description' => '<p>Panduan standar pengembangan aplikasi web untuk lingkungan Pemerintah Kabupaten Bojonegoro. Mencakup standar kode, arsitektur, dan deployment.</p>',
                'type' => VirtualGuideType::Web,
            ],
            [
                'name' => 'Panduan Desain API REST',
                'description' => '<p>Standar dan konvensi desain API RESTful untuk integrasi antar sistem pemerintah.</p>',
                'type' => VirtualGuideType::Web,
            ],
            [
                'name' => 'Panduan Keamanan Aplikasi',
                'description' => '<p>Panduan keamanan aplikasi web mengacu pada OWASP Top 10 dan standar keamanan pemerintah.</p>',
                'type' => VirtualGuideType::Web,
            ],
            [
                'name' => 'Panduan Manajemen Basis Data',
                'description' => '<p>Standar pengelolaan basis data, backup, migrasi, dan optimasi query untuk sistem pemerintah.</p>',
                'type' => VirtualGuideType::Web,
            ],
            [
                'name' => 'Panduan Deployment dan CI/CD',
                'description' => '<p>Prosedur deployment aplikasi web ke lingkungan staging dan production menggunakan pipeline CI/CD.</p>',
                'type' => VirtualGuideType::Web,
            ],
            [
                'name' => 'Panduan Monitoring dan Logging',
                'description' => '<p>Standar pemantauan performa aplikasi, pencatatan log, dan penanganan alerting.</p>',
                'type' => VirtualGuideType::Web,
            ],
            [
                'name' => 'Panduan Backup dan Pemulihan Data',
                'description' => '<p>Prosedur backup data berkala dan pemulihan sistem setelah insiden atau kegagalan.</p>',
                'type' => VirtualGuideType::Web,
            ],
            [
                'name' => 'Panduan Pengembangan Aplikasi Mobile',
                'description' => '<p>Standar pengembangan aplikasi mobile Android/iOS untuk layanan publik Kabupaten Bojonegoro.</p>',
                'type' => VirtualGuideType::Mobile,
            ],
            [
                'name' => 'Panduan Review Kode Aplikasi Mobile',
                'description' => '<p>Proses dan checklist code review untuk memastikan kualitas kode aplikasi mobile.</p>',
                'type' => VirtualGuideType::Mobile,
            ],
            [
                'name' => 'Panduan Publikasi ke App Store',
                'description' => '<p>Langkah-langkah publikasi aplikasi mobile ke Google Play Store dan Apple App Store.</p>',
                'type' => VirtualGuideType::Mobile,
            ],
        ];

        foreach ($guides as $data) {
            VirtualAssetGuide::firstOrCreate(
                ['name' => $data['name']],
                $data
            );
        }
    }
}
