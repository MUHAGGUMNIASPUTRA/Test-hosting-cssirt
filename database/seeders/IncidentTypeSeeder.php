<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\IncidentType;
use Illuminate\Support\Str;

class IncidentTypeSeeder extends Seeder
{
  public function run(): void
  {
    $types = [
      'Phishing' => 'Upaya penipuan untuk mencuri informasi sensitif.',
      'Malware' => 'Infeksi perangkat lunak berbahaya seperti virus, worm, atau ransomware.',
      'Defacement' => 'Perubahan tampilan halaman web secara tidak sah oleh peretas.',
      'Serangan DDoS' => 'Serangan yang membuat layanan online tidak dapat diakses oleh pengguna.',
      'Kebocoran Data' => 'Terungkapnya data rahasia atau pribadi ke pihak yang tidak berwenang.',
    ];

    foreach ($types as $name => $description) {
      IncidentType::create([
        'name' => $name,
        'slug' => Str::slug($name),
        'description' => $description,
      ]);
    }
  }
}
