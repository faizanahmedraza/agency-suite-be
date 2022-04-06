<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class AgencyRolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now()->toDateTimeString();

        $permissions = [
            ['name' => 'agency_access_all', 'guard_name' => 'api', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'agency_roles_all', 'guard_name' => 'api', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'agency_roles_read', 'guard_name' => 'api', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'agency_roles_create', 'guard_name' => 'api', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'agency_roles_update', 'guard_name' => 'api', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'agency_roles_delete', 'guard_name' => 'api', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'agency_permissions_list', 'guard_name' => 'api', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'agency_users_all', 'guard_name' => 'api', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'agency_users_read', 'guard_name' => 'api', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'agency_users_create', 'guard_name' => 'api', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'agency_users_update', 'guard_name' => 'api', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'agency_users_delete', 'guard_name' => 'api', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'agency_users_toggle_status', 'guard_name' => 'api', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'agency_request_service_read', 'guard_name' => 'api', 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('permissions')->insert($permissions);

        $role = Role::findByName('Agency');
        $role->givePermissionTo('agency_access_all');

    }
}
