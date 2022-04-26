<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class AgencyServiceSeeder extends Seeder
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
            ['name' => 'agency_services_all', 'guard_name' => 'api', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'agency_services_read', 'guard_name' => 'api', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'agency_services_create', 'guard_name' => 'api', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'agency_services_update', 'guard_name' => 'api', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'agency_services_delete', 'guard_name' => 'api', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'agency_services_toggle_status', 'guard_name' => 'api', 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('permissions')->insert($permissions);
    }
}
