<?php

namespace Database\Seeders;

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
        $roles = [
            ['name' => 'Super Admin', 'guard_name' => 'api'],
            ['name' => 'Admin', 'guard_name' => 'api'],
            ['name' => 'Business', 'guard_name' => 'api'],
            ['name' => 'Customer', 'guard_name' => 'api'],
            ['name' => 'User', 'guard_name' => 'api'],
        ];

        DB::table('roles')->insert($roles);
    }
}
