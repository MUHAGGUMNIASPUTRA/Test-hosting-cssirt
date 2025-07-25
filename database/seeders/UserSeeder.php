<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
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
      'role' => 'admin',
    ]);

    // Create a staff user
    User::create([
      'name' => 'Staf CSIRT',
      'email' => 'staff@csirt.bojonegorokab.go.id',
      'password' => Hash::make('password'),
      'role' => 'staff',
    ]);
  }
}
