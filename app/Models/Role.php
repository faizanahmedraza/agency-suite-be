<?php

namespace App\Models;

use App\Http\Traits\UserAuditTrait;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use UserAuditTrait;

    const ROLES_PREFIXES = ['admin'=>'admin_'];

    const RESTRICT_ROLES = ['Super Admin','Agency','Customer'];
}
