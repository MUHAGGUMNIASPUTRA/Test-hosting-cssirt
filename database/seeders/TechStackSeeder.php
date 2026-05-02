<?php

namespace Database\Seeders;

use App\Models\TechStack;
use App\Models\TechStackCategory;
use Illuminate\Database\Seeder;

class TechStackSeeder extends Seeder
{
    public function run(): void
    {
        $getCategory = fn (string $name) => TechStackCategory::where('name', $name)->first()?->id;

        $stacks = [
            ['name' => 'Nginx', 'description' => 'High-performance web server dan reverse proxy.', 'category' => 'Web Server'],
            ['name' => 'Apache HTTP Server', 'description' => 'Web server open-source yang paling banyak digunakan.', 'category' => 'Web Server'],
            ['name' => 'Laravel', 'description' => 'PHP framework dengan ekosistem lengkap untuk pengembangan web.', 'category' => 'Framework'],
            ['name' => 'Next.js', 'description' => 'React framework untuk aplikasi web full-stack dengan SSR.', 'category' => 'Framework'],
            ['name' => 'Vue.js', 'description' => 'JavaScript framework progresif untuk membangun antarmuka pengguna.', 'category' => 'Framework'],
            ['name' => 'PHP', 'description' => 'Bahasa pemrograman server-side yang banyak digunakan untuk web.', 'category' => 'Language'],
            ['name' => 'Golang', 'description' => 'Bahasa pemrograman kompilasi yang efisien dari Google.', 'category' => 'Language'],
            ['name' => 'JavaScript', 'description' => 'Bahasa pemrograman utama untuk pengembangan web frontend maupun backend.', 'category' => 'Language'],
            ['name' => 'TypeScript', 'description' => 'Superset JavaScript dengan static typing untuk kode yang lebih aman.', 'category' => 'Language'],
            ['name' => 'PostgreSQL', 'description' => 'Sistem manajemen basis data relasional open-source yang canggih.', 'category' => 'Database'],
            ['name' => 'MariaDB', 'description' => 'Fork MySQL yang dikembangkan komunitas dengan fitur tambahan.', 'category' => 'Database'],
            ['name' => 'MySQL', 'description' => 'Sistem manajemen basis data relasional open-source yang populer.', 'category' => 'Database'],
            ['name' => 'Docker', 'description' => 'Platform containerisasi untuk pengembangan dan deployment aplikasi.', 'category' => 'DevOps & Infrastructure'],
            ['name' => 'Redis', 'description' => 'In-memory data store untuk caching, session, dan message broker.', 'category' => 'Database'],
        ];

        foreach ($stacks as $data) {
            TechStack::firstOrCreate(
                ['name' => $data['name']],
                [
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'category_id' => $getCategory($data['category']),
                ]
            );
        }
    }
}
