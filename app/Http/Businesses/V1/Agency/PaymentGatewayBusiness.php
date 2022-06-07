<?php

namespace App\Http\Businesses\V1\Agency;

use App\Http\Services\V1\Agency\PaymentGatewayService;
use Illuminate\Http\Request;

class PaymentGatewayBusiness
{
    public static function first(Request $request,$bypass = false)
    {
        return PaymentGatewayService::first($request,$bypass);
    }

    public static function create(Request $request)
    {
        $gateway = self::first($request,true);
        return PaymentGatewayService::create($request,$gateway);
    }

}
