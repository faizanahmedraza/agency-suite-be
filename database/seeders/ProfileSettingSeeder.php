<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ProfileSettingSeeder extends Seeder
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

        $arrayOfPermissionNames = [
            'agency_profile_all',
            'agency_profile_read',
            'agency_profile_update',
        ];

        $permissions = collect($arrayOfPermissionNames)->map(function ($permission) {
            return ['name' => $permission, 'guard_name' => 'api', 'created_at' => Carbon::now()->toDateTimeString()];
        });

        Permission::insert($permissions->toArray());

        $role = Role::findByName('Customer');
        $role->givePermissionTo($arrayOfPermissionNames);
    }
}
