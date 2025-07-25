<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Incident;
use App\Models\IncidentType;
use App\Models\User;

class IncidentSeeder extends Seeder
{
  public function run(): void
  {
    $staffUser = User::where('role', 'staff')->first();
    $incidentTypes = IncidentType::all();

    Incident::create([
      'case_id' => 'CSIRT-BJN-2025-001',
      'reporter_name' => 'Budi Santoso',
      'reporter_email' => 'budi.santoso@opd.go.id',
      'reporter_phone' => '081234567890',
      'incident_type_id' => $incidentTypes->where('slug', 'phishing')->first()->id,
      'description' => 'Menerima email mencurigakan yang mengaku dari pihak Bank Jatim, meminta untuk klik link dan mengisi data pribadi.',
      'incident_at' => now()->subHours(3),
      'status' => 'Dalam Penyelidikan',
      'priority' => 'Tinggi',
      'assigned_to' => $staffUser->id,
    ]);

    Incident::create([
      'case_id' => 'CSIRT-BJN-2025-002',
      'reporter_name' => 'Siti Aminah',
      'reporter_email' => 'siti.aminah@opd.go.id',
      'reporter_phone' => '081234567891',
      'incident_type_id' => $incidentTypes->where('slug', 'malware')->first()->id,
      'description' => 'Komputer di ruang pelayanan tiba-tiba menjadi lambat dan menampilkan banyak iklan aneh. Diduga terinfeksi malware.',
      'incident_at' => now()->subDays(1),
      'status' => 'Baru',
      'priority' => 'Sedang',
      'assigned_to' => null,
    ]);
  }
}
