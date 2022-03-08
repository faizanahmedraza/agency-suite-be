<?php

namespace App\Http\Businesses\V1\Admin;

use App\Exceptions\V1\UserException;
use App\Http\Services\V1\Admin\PermissionService;
use App\Http\Services\V1\Admin\RoleService;
use App\Http\Services\V1\Admin\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserBusiness
{
    public static function first(int $id)
    {
        return UserService::first($id);
    }

    public static function store($request)
    {
        // create user
        $user = UserService::store($request);

        //assigning roles to the user
        RoleService::assignRolesToUser($request, $user);

        // assign direct permission to user
        PermissionService::assignDirectPermissionToUser($request, $user);

        return $user;
    }

    public static function get($request)
    {
        // get user
        return UserService::get($request);
    }

    public static function update($request, int $id)
    {
        $user = UserService::first($id);

        // update in users table
        UserService::update($request, $user);

        RoleService::syncRolesToUser($request, $user);

        PermissionService::assignDirectPermissionToUser($request, $user);

        return $user;
    }

    public static function destroy(int $id) : void
    {
        // delete user
        $user = UserService::first($id);

        if ($user->id == Auth::id()) {
            throw UserException::authUserRestrictStatus();
        }

        UserService::destroy($user);
    }

    public static function toggleStatus($id)
    {
        // get user
        $user = UserService::first($id);

        if ($user->id == Auth::id()) {
            throw UserException::authUserRestrictStatus();
        }
        // status toggle
        return  UserService::toggleStatus($user);
    }

    public  static  function changeAnyPassword($request,$id)
    {
        $user = UserService::first($id);
        UserService::changePassword($user,$request);
    }
}
