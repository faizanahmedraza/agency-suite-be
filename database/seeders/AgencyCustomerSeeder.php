<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AgencyCustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $now = Carbon::now()->toDateTimeString();

        $permissions = [
            ['name' => 'agency_customers_all', 'guard_name' => 'api', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'agency_customers_read', 'guard_name' => 'api', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'agency_customers_create', 'guard_name' => 'api', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'agency_customers_update', 'guard_name' => 'api', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'agency_customers_delete', 'guard_name' => 'api', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'agency_customers_toggle_status', 'guard_name' => 'api', 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('permissions')->insert($permissions);
    }
}
