<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
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
            IpAddressSeeder::class,
            SubdomainSeeder::class,
            PhysicalAssetSeeder::class,
            InformationAssetSeeder::class,
        ]);
    }
}
