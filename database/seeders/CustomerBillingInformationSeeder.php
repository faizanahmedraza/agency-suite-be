<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class CustomerBillingInformationSeeder extends Seeder
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
            'agency_customers_billing_information_all',
            'agency_customers_billing_information_read',
            'agency_customers_billing_information_create',
            'agency_customers_billing_information_update',
            'agency_customers_billing_information_delete',
        ];

        $permissions = collect($arrayOfPermissionNames)->map(function ($permission) {
            return ['name' => $permission, 'guard_name' => 'web', 'created_at' => Carbon::now()->toDateTimeString()];
        });

        Permission::insert($permissions->toArray());
    }
}
