<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use Illuminate\Support\Str;

class ServiceSeeder extends Seeder
{
  public function run(): void
  {
    $services = [
      ['name' => 'Penanganan Insiden', 'icon' => 'pi-shield', 'desc' => 'Memberikan respons cepat dan tuntas terhadap insiden keamanan siber.'],
      ['name' => 'Notifikasi Keamanan', 'icon' => 'pi-bell', 'desc' => 'Menyebarkan informasi dan peringatan dini terkait potensi ancaman siber.'],
      ['name' => 'Edukasi & Panduan', 'icon' => 'pi-book', 'desc' => 'Meningkatkan kesadaran keamanan melalui panduan, artikel, dan pelatihan.'],
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
