<?php

namespace Database\Seeders;

use App\Models\Vendor;
use Illuminate\Database\Seeder;

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        $vendors = [
            [
                'company_name' => 'PT Telkom Indonesia (Persero) Tbk',
                'location' => 'Jakarta Selatan',
                'phone' => '1500250',
                'email' => 'info@telkom.co.id',
                'pic_name' => 'Andi Wijaya',
                'pic_phone' => '08111234567',
                'pic_email' => 'andi.wijaya@telkom.co.id',
            ],
            [
                'company_name' => 'PT Lintasarta',
                'location' => 'Jakarta Pusat',
                'phone' => '(021) 23456789',
                'email' => 'info@lintasarta.net',
                'pic_name' => 'Sari Dewi',
                'pic_phone' => '08112345678',
                'pic_email' => 'sari.dewi@lintasarta.net',
            ],
            [
                'company_name' => 'CV Solusi Digital Nusantara',
                'location' => 'Surabaya',
                'phone' => '(031) 7654321',
                'email' => 'info@solusinusantara.co.id',
                'pic_name' => 'Rizky Pratama',
                'pic_phone' => '08113456789',
                'pic_email' => 'rizky@solusinusantara.co.id',
            ],
            [
                'company_name' => 'PT Sigma Cipta Caraka',
                'location' => 'Tangerang Selatan',
                'phone' => '(021) 8765432',
                'email' => 'contact@sigma.co.id',
                'pic_name' => 'Budi Prasetyo',
                'pic_phone' => '08114567890',
                'pic_email' => 'budi.p@sigma.co.id',
            ],
            [
                'company_name' => 'PT Indosat Ooredoo Hutchison',
                'location' => 'Jakarta Pusat',
                'phone' => '185',
                'email' => 'corporate@indosatooredoo.com',
                'pic_name' => null,
                'pic_phone' => null,
                'pic_email' => null,
            ],
            [
                'company_name' => 'CV Inovasi Teknologi Mandiri',
                'location' => 'Bojonegoro',
                'phone' => '(0353) 891234',
                'email' => 'info@inovasitm.co.id',
                'pic_name' => 'Haris Purnomo',
                'pic_phone' => '08115678901',
                'pic_email' => 'haris@inovasitm.co.id',
            ],
            [
                'company_name' => 'PT Mitra Integrasi Informatika',
                'location' => 'Surabaya',
                'phone' => '(031) 5432109',
                'email' => 'info@mii.co.id',
                'pic_name' => 'Wulan Sari',
                'pic_phone' => '08116789012',
                'pic_email' => 'wulan@mii.co.id',
            ],
            [
                'company_name' => 'PT Astra Graphia Information Technology',
                'location' => 'Jakarta Barat',
                'phone' => '(021) 6543210',
                'email' => 'info@agit.id',
                'pic_name' => null,
                'pic_phone' => null,
                'pic_email' => null,
            ],
        ];

        foreach ($vendors as $data) {
            Vendor::firstOrCreate(
                ['company_name' => $data['company_name']],
                $data
            );
        }
    }
}
