<?php

namespace App\Http\Businesses\V1\Admin;

use App\Http\Services\V1\Admin\PermissionService;

class PermissionBusiness
{
    public static function get()
    {
        return PermissionService::get();
    }
}
