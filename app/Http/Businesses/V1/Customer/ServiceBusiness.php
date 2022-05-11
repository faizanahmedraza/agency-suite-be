<?php

namespace App\Http\Businesses\V1\Customer;

use App\Http\Services\V1\Customer\ServiceBusinessService;
use Illuminate\Http\Request;

class ServiceBusiness
{
    public static function first($id)
    {
        return ServiceBusinessService::first($id);
    }

    public static function get(Request $request)
    {
        return ServiceBusinessService::get($request);
    }
}
