<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         $this->call([
             RolePermissionSeeder::class,
             AgencyRolePermissionSeeder::class,
             AdminSeeder::class,
             AgencyServiceSeeder::class,
             AgencyPortalSettingSeeder::class,
             AgencyCustomerSeeder::class,
         ]);
    }
}
