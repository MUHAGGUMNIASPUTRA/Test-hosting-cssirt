<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\IncidentLog;
use App\Models\Incident;
use App\Models\User;

class IncidentLogSeeder extends Seeder
{
  public function run(): void
  {
    $incident1 = Incident::where('case_id', 'CSIRT-2025-08-001')->first();
    $adminUser = User::where('role', 'admin')->first();
    $staffUser = User::where('role', 'staff')->first();

    if ($incident1) {
      IncidentLog::create([
        'incident_id' => $incident1->id,
        'user_id' => $adminUser->id,
        'log_message' => 'Insiden diterima dan diverifikasi. Diteruskan ke Staf Teknis.',
      ]);
      IncidentLog::create([
        'incident_id' => $incident1->id,
        'user_id' => $staffUser->id,
        'log_message' => 'Menghubungi pelapor untuk meminta screenshot email phishing.',
      ]);
    }
  }
}
