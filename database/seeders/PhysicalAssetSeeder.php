<?php

namespace Database\Seeders;

use App\Models\PhysicalAsset;
use Illuminate\Database\Seeder;

class PhysicalAssetSeeder extends Seeder
{
    public function run(): void
    {
        $assets = [
            [
                'asset_code' => 'FISIK-001',
                'name' => 'Server Dell PowerEdge R740',
                'description' => 'Server rack utama untuk hosting aplikasi',
                'specifications' => 'Intel Xeon Gold 6226R, 64GB RAM, 2x 1.2TB SAS HDD',
                'year' => 2022,
            ],
            [
                'asset_code' => 'FISIK-002',
                'name' => 'Switch Cisco Catalyst 2960',
                'description' => 'Switch jaringan 24 port',
                'specifications' => '24x 10/100/1000T, 4x SFP, 370W PoE',
                'year' => 2021,
            ],
            [
                'asset_code' => 'FISIK-003',
                'name' => 'UPS APC Smart-UPS 3000',
                'description' => 'Uninterruptible power supply server room',
                'specifications' => '3000VA/2700W, runtime 10 menit full load',
                'year' => 2020,
            ],
            [
                'asset_code' => 'FISIK-004',
                'name' => 'Laptop Lenovo ThinkPad T490',
                'description' => 'Laptop kerja tim analis CSIRT',
                'specifications' => 'Intel Core i5-8265U, 8GB RAM, 256GB SSD',
                'year' => 2023,
            ],
            [
                'asset_code' => 'FISIK-005',
                'name' => 'Firewall Fortinet FortiGate 100F',
                'description' => 'Next-generation firewall jaringan kantor',
                'specifications' => '20Gbps firewall throughput, 1Gbps NGFW',
                'year' => 2022,
            ],
        ];

        foreach ($assets as $asset) {
            PhysicalAsset::create($asset);
        }
    }
}
