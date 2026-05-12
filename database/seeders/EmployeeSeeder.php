<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Organization;
use App\Models\Position;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $diskominfo = Organization::where('name', 'like', '%Komunikasi dan Informatika%')->first();

        if (! $diskominfo) {
            return;
        }

        $getPosition = fn (string $name) => Position::where('name', $name)->first();

        $employees = [
            [
                'name' => 'Drs. Agus Supriyanto, M.Si',
                'nip' => '196804121990031005',
                'nik' => '3522041204680003',
                'phone' => '08123456789',
                'email' => 'kadis.diskominfo@bojonegorokab.go.id',
                'position' => 'Kepala Dinas',
                'year_joined' => 1990,
                'is_active' => true,
            ],
            [
                'name' => 'Ir. Siti Rahayu, M.T',
                'nip' => '197203251998032002',
                'nik' => '3522032503720001',
                'phone' => '08234567890',
                'email' => 'sekretaris.diskominfo@bojonegorokab.go.id',
                'position' => 'Sekretaris',
                'year_joined' => 1998,
                'is_active' => true,
            ],
            [
                'name' => 'Budi Santoso, S.Kom',
                'nip' => '198501152010011003',
                'nik' => '3522011501850002',
                'phone' => '08345678901',
                'email' => 'budi.santoso@diskominfo.go.id',
                'position' => 'Kepala Bidang Teknologi Informasi dan Komunikasi',
                'year_joined' => 2010,
                'is_active' => true,
            ],
            [
                'name' => 'Hendra Kurniawan, S.Kom',
                'nip' => '198803202012031002',
                'nik' => '3522032003880001',
                'phone' => '08456789012',
                'email' => 'hendra.k@diskominfo.go.id',
                'position' => 'Pranata Komputer Ahli Muda',
                'year_joined' => 2012,
                'is_active' => true,
            ],
            [
                'name' => 'Rina Wulandari, S.T',
                'nip' => '199001052015042003',
                'nik' => '3522010501900002',
                'phone' => '08567890123',
                'email' => 'rina.wulandari@diskominfo.go.id',
                'position' => 'Pranata Komputer Ahli Pertama',
                'year_joined' => 2015,
                'is_active' => true,
            ],
            [
                'name' => 'Doni Firmansyah, S.Kom',
                'nip' => '199204182017031005',
                'nik' => '3522041804920001',
                'phone' => '08678901234',
                'email' => 'doni.f@diskominfo.go.id',
                'position' => 'Pengelola Sistem dan Prosedur',
                'year_joined' => 2017,
                'is_active' => true,
            ],
            [
                'name' => 'Anisa Putri, A.Md',
                'nip' => '199507082019032007',
                'nik' => '3522070807950001',
                'phone' => '08789012345',
                'email' => 'anisa.putri@diskominfo.go.id',
                'position' => 'Staff Administrasi',
                'year_joined' => 2019,
                'is_active' => true,
            ],
            [
                'name' => 'Fajar Setiawan, S.Kom',
                'nip' => '199309152020011002',
                'nik' => '3522091503930001',
                'phone' => '08890123456',
                'email' => 'fajar.setiawan@diskominfo.go.id',
                'position' => 'Analis Kebijakan Ahli Muda',
                'year_joined' => 2020,
                'is_active' => true,
            ],
            [
                'name' => 'Dewi Lestari, S.E',
                'nip' => '198712272016042001',
                'nik' => '3522272712870001',
                'phone' => '08901234567',
                'email' => 'dewi.lestari@diskominfo.go.id',
                'position' => 'Staff Administrasi',
                'year_joined' => 2016,
                'is_active' => false,
            ],
            [
                'name' => 'Yusuf Hidayat, S.T',
                'nip' => '199106132018031003',
                'nik' => '3522130601910001',
                'phone' => '08012345678',
                'email' => 'yusuf.hidayat@diskominfo.go.id',
                'position' => 'Kepala Bidang E-Government',
                'year_joined' => 2018,
                'is_active' => true,
            ],
        ];

        foreach ($employees as $data) {
            $position = $getPosition($data['position']);
            // Gunakan name sebagai key karena email disimpan terenkripsi (tidak bisa di-query langsung)
            Employee::firstOrCreate(
                ['name' => $data['name']],
                [
                    'nip' => $data['nip'],
                    'nik' => $data['nik'],
                    'phone' => $data['phone'],
                    'email' => $data['email'],
                    'position_id' => $position?->id,
                    'organization_id' => $diskominfo->id,
                    'year_joined' => $data['year_joined'],
                    'is_active' => $data['is_active'],
                ]
            );
        }
    }
}
