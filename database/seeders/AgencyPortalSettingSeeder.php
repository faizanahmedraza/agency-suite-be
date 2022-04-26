<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class AgencyPortalSettingSeeder extends Seeder
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
            ['name' => 'agency_portal_settings_all', 'guard_name' => 'api', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'agency_portal_settings_update', 'guard_name' => 'api', 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('permissions')->insert($permissions);
    }
}
