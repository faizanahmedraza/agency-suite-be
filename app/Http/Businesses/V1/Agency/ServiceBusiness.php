<?php

namespace App\Http\Businesses\V1\Agency;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceBusiness
{
    public static function store(Request $request)
    {
        // create agency users
        $user = (new UserService())->create($request, Auth::user()->agency);

        //assigning roles to the user
        RoleService::assignRolesToUser($request, $user);

        // assign direct permission to user
        PermissionService::assignDirectPermissionToUser($request, $user);

        return $user->load(['roles', 'roles.permissions', 'permissions']);
    }
}
