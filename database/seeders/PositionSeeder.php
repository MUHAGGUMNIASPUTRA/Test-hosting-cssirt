<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Organization;
use App\Models\Position;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        $diskominfo = Organization::where('name', 'like', '%Komunikasi dan Informatika%')->first();

        if (! $diskominfo) {
            return;
        }

        $getDept = fn (string $name) => Department::where('organization_id', $diskominfo->id)
            ->where('name', $name)
            ->first();

        $positions = [
            ['name' => 'Kepala Dinas', 'department' => 'Pimpinan'],
            ['name' => 'Kepala Bidang Teknologi Informasi dan Komunikasi', 'department' => 'Bidang Teknologi Informasi dan Komunikasi'],
            ['name' => 'Kepala Bidang Pengelolaan Informasi dan Komunikasi Publik', 'department' => 'Bidang Pengelolaan Informasi dan Komunikasi Publik'],
            ['name' => 'Kepala Bidang E-Government', 'department' => 'Bidang E-Government'],
            ['name' => 'Sekretaris', 'department' => 'Sekretariat'],
            ['name' => 'Analis Kebijakan Ahli Muda', 'department' => 'Bidang Teknologi Informasi dan Komunikasi'],
            ['name' => 'Pranata Komputer Ahli Muda', 'department' => 'Bidang Teknologi Informasi dan Komunikasi'],
            ['name' => 'Pranata Komputer Ahli Pertama', 'department' => 'Bidang E-Government'],
            ['name' => 'Pengelola Sistem dan Prosedur', 'department' => 'Bidang E-Government'],
            ['name' => 'Staff Administrasi', 'department' => 'Sekretariat'],
        ];

        foreach ($positions as $data) {
            $dept = $getDept($data['department']);
            if (! $dept) {
                continue;
            }
            Position::firstOrCreate(
                ['name' => $data['name'], 'department_id' => $dept->id],
            );
        }
    }
}
