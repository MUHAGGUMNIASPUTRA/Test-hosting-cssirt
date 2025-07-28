<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use Illuminate\Support\Str;

class ServiceSeeder extends Seeder
{
  public function run(): void
  {
    Service::truncate();

    $services = [
      ['name' => 'Penanganan Insiden', 'icon' => 'pi-shield', 'desc' => 'Memberikan respons cepat saat terjadi insiden siber, seperti peretasan atau serangan malware, termasuk analisis penyebab, pemulihan sistem, dan pencegahan agar kejadian serupa tidak terulang.'],
      ['name' => 'Notifikasi Keamanan', 'icon' => 'pi-bell', 'desc' => 'Menyebarkan informasi penting terkait ancaman keamanan, seperti celah kerentanan, aktivitas mencurigakan, atau tren serangan terkini agar pengguna dapat segera melakukan tindakan pengamanan.'],
      ['name' => 'Edukasi & Panduan', 'icon' => 'pi-book', 'desc' => 'Menyediakan berbagai materi edukatif berupa artikel, infografis, dan pelatihan teknis dasar yang membantu pengguna memahami risiko siber dan cara menghadapinya dengan langkah yang tepat.'],
    ];

    foreach ($services as $service) {
      Service::create([
        'name' => $service['name'],
        'slug' => Str::slug($service['name']),
        'icon' => $service['icon'],
        'short_description' => $service['desc'],
        'is_active' => true,
      ]);
    }
  }
}
