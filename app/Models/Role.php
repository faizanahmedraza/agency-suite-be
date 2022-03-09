<?php

namespace App\Models;

use App\Http\Traits\UserAuditTrait;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use UserAuditTrait;

    const ROLES_PREFIXES = ['admin'=>'admin_'];

    const RESTRICT_ROLES = ['Super Admin','Agency','Customer'];

    public function scopeUserRoles($query)
    {
        return $query->where('created_by', Auth::id());
    }
}
