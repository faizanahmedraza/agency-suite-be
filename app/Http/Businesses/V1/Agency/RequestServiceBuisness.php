<?php

namespace App\Http\Businesses\V1\Agency;

use App\Http\Services\V1\Agency\CustomerServiceRequestService;

class RequestServiceBuisness
{
    public static function get($request)
    {
        return CustomerServiceRequestService::get($request);
    }


    public static function first($id)
    {
        return CustomerServiceRequestService::first($id,[]);
    }

}
