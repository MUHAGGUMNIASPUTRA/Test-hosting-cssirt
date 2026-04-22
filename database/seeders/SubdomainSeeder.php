<?php

namespace Database\Seeders;

use App\Models\Subdomain;
use Illuminate\Database\Seeder;

class SubdomainSeeder extends Seeder
{
    public function run(): void
    {
        $subdomains = [
            ['subdomain' => 'csirt.bojonegorokab.go.id', 'description' => 'Portal utama CSIRT Bojonegoro'],
            ['subdomain' => 'api.csirt.bojonegorokab.go.id', 'description' => 'API gateway layanan CSIRT'],
            ['subdomain' => 'mail.bojonegorokab.go.id', 'description' => 'Server email Pemkab Bojonegoro'],
            ['subdomain' => 'sso.bojonegorokab.go.id', 'description' => 'Single sign-on Pemkab'],
            ['subdomain' => 'simda.bojonegorokab.go.id', 'description' => 'Sistem Informasi Manajemen Daerah'],
        ];

        foreach ($subdomains as $subdomain) {
            Subdomain::create($subdomain);
        }
    }
}
