<?php

namespace Database\Seeders;

use App\Models\TechStackCategory;
use Illuminate\Database\Seeder;

class TechStackCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Web Server', 'description' => 'Perangkat lunak yang melayani konten web melalui protokol HTTP/HTTPS.'],
            ['name' => 'Framework', 'description' => 'Kerangka kerja pengembangan aplikasi web maupun mobile.'],
            ['name' => 'Language', 'description' => 'Bahasa pemrograman yang digunakan dalam pengembangan aplikasi.'],
            ['name' => 'Database', 'description' => 'Sistem manajemen basis data untuk penyimpanan dan pengelolaan data.'],
            ['name' => 'DevOps & Infrastructure', 'description' => 'Tools dan platform untuk deployment, CI/CD, dan manajemen infrastruktur.'],
        ];

        foreach ($categories as $data) {
            TechStackCategory::firstOrCreate(['name' => $data['name']], $data);
        }
    }
}
