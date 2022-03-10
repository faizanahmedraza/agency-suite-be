<?php

namespace App\Models;

use App\Http\Traits\UserAuditTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use UserAuditTrait;

    const ROLES_PREFIXES = ['admin'=>'admin_','agency'=>'agency_'];

    const RESTRICT_ROLES = ['Super Admin','Agency','Customer'];

    public function scopeUserRoles($query)
    {
        return $query->where('created_by', Auth::id());
    }

    public function scopeUserPermissions($query,$prefix)
    {
        return $query->whereHas('permissions', function (Builder $query) use($prefix) {
            $query->where('name', 'like', $prefix.'%');
        });
    }
}
