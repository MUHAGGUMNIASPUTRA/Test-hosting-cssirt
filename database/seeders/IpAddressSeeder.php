<?php

namespace Database\Seeders;

use App\Models\IpAddress;
use Illuminate\Database\Seeder;

class IpAddressSeeder extends Seeder
{
    public function run(): void
    {
        $ipAddresses = [
            ['private_ip' => '192.168.1.10', 'public_ip' => '103.10.20.5', 'description' => 'Server utama aplikasi web CSIRT'],
            ['private_ip' => '192.168.1.20', 'public_ip' => null, 'description' => 'Server database internal'],
            ['private_ip' => '192.168.1.30', 'public_ip' => '103.10.20.6', 'description' => 'Server monitoring'],
            ['private_ip' => '10.0.0.5', 'public_ip' => null, 'description' => 'Server backup internal'],
            ['private_ip' => '172.16.0.1', 'public_ip' => '203.189.92.10', 'description' => 'Load balancer utama'],
        ];

        foreach ($ipAddresses as $ip) {
            IpAddress::create($ip);
        }
    }
}
