<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create an admin user
        User::create([
            'name' => 'Admin CSIRT',
            'email' => 'admin@csirt.bojonegorokab.go.id',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'role' => 'admin',
        ]);

        // Create a staff user
        User::create([
            'name' => 'Staf CSIRT',
            'email' => 'staff@csirt.bojonegorokab.go.id',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'role' => 'staff',
        ]);
    }
}
