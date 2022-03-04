<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now()->toDateTimeString();

        $roles = [
            ['name' => 'Super Admin', 'guard_name' => 'api', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Admin', 'guard_name' => 'api', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Agency', 'guard_name' => 'api', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Customer', 'guard_name' => 'api', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'User', 'guard_name' => 'api', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Finance', 'guard_name' => 'api', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'HR', 'guard_name' => 'api', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Accounts', 'guard_name' => 'api', 'created_at' => $now, 'updated_at' => $now],
        ];

        $permissions = [
            ['name' => 'access_all', 'guard_name' => 'api', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'roles_all', 'guard_name' => 'api', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'roles_list', 'guard_name' => 'api', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'roles_create', 'guard_name' => 'api', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'roles_update', 'guard_name' => 'api', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'roles_delete', 'guard_name' => 'api', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'permissions_list', 'guard_name' => 'api', 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('permissions')->insert($permissions);
        DB::table('roles')->insert($roles);

        $role = Role::findByName('Super Admin');
        $role->givePermissionTo('access_all');

    }
}
