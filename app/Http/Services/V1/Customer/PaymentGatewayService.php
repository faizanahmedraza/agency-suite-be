<?php

namespace App\Http\Services\V1\Customer;

use App\Exceptions\V1\PaymentException;
use App\Models\PaymentGateway;

class PaymentGatewayService
{
    public static function first($gateway = "stripe")
    {
        $gateway = PaymentGateway::with(['agency'])
            ->where('gateway', clean($gateway))
            ->where('agency_id', app('agency')->id)
            ->where('is_enable', true)
            ->first();

        if (!$gateway) {
            throw PaymentException::gatewayNotFound();
        }

        return $gateway;
    }
}
