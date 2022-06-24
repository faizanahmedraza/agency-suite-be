<?php

namespace App\Http\Businesses\V1\Agency;

use App\Http\Services\V1\Agency\PaymentGatewayService;
use Illuminate\Http\Request;

class PaymentGatewayBusiness
{
    public static function first($gateway, $bypass = true)
    {
        return PaymentGatewayService::first($gateway, $bypass);
    }

    public static function create(Request $request)
    {
        $request->gateway = (isset($request->gateway) && !empty($request->gateway)) ? clean($request->gateway) : "stripe";
        $paymentGateway = self::first("stripe", true);
        return PaymentGatewayService::create($request, $paymentGateway);
    }

    public static function changeStatus($gateway)
    {
        $paymentGateway = self::first($gateway, true);
        PaymentGatewayService::changeStatus($paymentGateway);
    }
}
