<?php

namespace App\Http\Businesses\V1\Agency;

use App\Http\Services\V1\Agency\PermissionService;

class PermissionBusiness
{
    public static function get()
    {
        return PermissionService::get();
    }
}
