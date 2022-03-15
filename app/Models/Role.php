<?php

namespace App\Models;

use App\Exceptions\V1\DomainException;
use App\Http\Traits\UserAuditTrait;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use UserAuditTrait;

    const ROLES_PREFIXES = ['admin'=>'admin_','agency' => 'agency_'];

    const RESTRICT_ROLES = ['Super Admin','Agency','Customer'];

    public function scopeAdminRoles($query)
    {
        return $query->where(function($query) {
            $query->where('name', 'not like','agency_%')
                ->whereNotIn('name',self::RESTRICT_ROLES);
        });
    }

    public function scopeAgencyRoles($query)
    {
        return $query->where('agency_id', app('agency')->id);
    }
}
