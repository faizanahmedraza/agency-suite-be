<?php

namespace App\Http\Businesses\V1\Admin;

use Illuminate\Http\Request;
use App\Http\Services\V1\Admin\RoleService;

class RoleBusiness
{
    public static function store(Request $request)
    {
        return RoleService::store($request);
    }

    public static function get()
    {
        return RoleService::get();
    }

    public static function first(int $id)
    {
        return RoleService::first($id);
    }

    public static function update(Request $request, int $id)
    {
        $role = RoleService::avoidRoleFirst($id);
        return RoleService::update($request, $role);
    }

    public static function destroy(int $id): void
    {
        $role = RoleService::avoidRoleFirst($id);
        RoleService::destroy($role);
    }
}
