<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //   'name' => 'Test User',
        //   'email' => 'test@example.com',
        // ]);

        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            TagSeeder::class,
            PostSeeder::class,
            AnnouncementSeeder::class,
            IncidentSeeder::class,
            IncidentLogSeeder::class,
            DocumentSeeder::class,
            ServiceSeeder::class,
            OrganizationSeeder::class,
            DepartmentSeeder::class,
            PositionSeeder::class,
            LocationSeeder::class,
            EmployeeSeeder::class,
            VendorSeeder::class,
            TechStackCategorySeeder::class,
            TechStackSeeder::class,
            WebApplicationSeeder::class,
            MobileApplicationSeeder::class,
            LicenseSeeder::class,
            VirtualAssetGuideSeeder::class,
        ]);
    }
}
