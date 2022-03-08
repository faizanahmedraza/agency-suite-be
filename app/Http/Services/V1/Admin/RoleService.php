<?php

namespace App\Http\Services\V1\Admin;

use App\Exceptions\V1\RoleException;
use App\Models\User;
use Illuminate\Http\Request;
use App\Exceptions\V1\ModelException;
use App\Exceptions\V1\FailureException;
use App\Models\Role;


class RoleService
{
    // Admin and Customer Roles Are Restricted
    public static function restrictRoles()
    {
        return config('permission.restrict_roles');
    }

    public static function get()
    {
        $role = Role::with('permissions')->latest()->get();
        return $role;
    }

    public static function store(Request $request) : Role
    {
        $role = new Role();
        $role->name = Role::ROLES_PREFIXES['admin'].$request->name;
        $role->save();

        if (!$role) {
            throw FailureException::serverError();
        }

        PermissionService::assigPermissionsToRole($role, $request);

        return $role;
    }

    public static function first($id)
    {
        $role = Role::with('permissions')->find($id);

        if (!$role) {
            throw ModelException::dataNotFound();
        }

        return $role;
    }

    public static function update(Request $request, Role $role) : Role
    {
        $role->name = Role::ROLES_PREFIXES['admin'].$request->name;
        $role->save();

        // re asssign new permissions
        PermissionService::assigPermissionsToRole($role, $request);

        if (!$role) {
            throw FailureException::serverError();
        }

        return $role;
    }

    public static function destroy(Role $role) : void
    {
        $role->delete();
    }

    public static function avoidRoleFirst(int $id)
    {
        $role = Role::where('id', $id)
        ->whereNotIn('name', self::restrictRoles())
        ->first();

        if (!$role) {
            throw ModelException::dataNotFound();
        }

        return $role;
    }

    public static function assignRolesToUser(Request $request, User $user)
    {
        if (!empty($request->get('roles'))) {
            try {
                return $user->assignRole($request['roles']);
            } catch (\Spatie\Permission\Exceptions\RoleDoesNotExist $exception) {
                throw RoleException::invalidRole();
            }
        }
    }

    public static function syncRolesToUser(Request $request, User $user)
    {
        if (!empty($request->get('roles'))) {
            try {
                return $user->syncRoles($request['roles']);
            } catch (\Spatie\Permission\Exceptions\RoleDoesNotExist $exception) {
                throw RoleException::invalidRole();
            }
        }
    }
}
