<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AgencyServiceRequestSeeder extends Seeder
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
            'agency_services_request_all',
            'agency_services_request_read',
            'agency_services_request_create',
            'agency_services_request_update',
            'agency_services_request_delete',
        ];

        $permissions = collect($arrayOfPermissionNames)->map(function ($permission) {
            return ['name' => $permission, 'guard_name' => 'api', 'created_at' => Carbon::now()->toDateTimeString()];
        });

        Permission::insert($permissions->toArray());

        $role = Role::findByName('Customer');
        $role->givePermissionTo($arrayOfPermissionNames);
    }
}
