<?php

namespace App\Http\Services\V1\Agency;

use App\Models\PaymentGateway;
use Illuminate\Http\Request;

use App\Exceptions\V1\ModelException;
use App\Exceptions\V1\FailureException;

class PaymentGatewayService
{
    public static function create(Request $request, $gateway = null)
    {
        if (!empty($gateway)) {
            $gateway->delete();
        }
        $gateway = new PaymentGateway();
        $gateway->gateway = (isset($request->gateway) && !empty($request->gateway)) ? clean($request->gateway) : "stripe";
        $gateway->gateway_id = null;
        $gateway->gateway_secret = trim($request->gateway_secret);
        $gateway->agency_id = app('agency')->id;
        $gateway->is_enable = true;
        $gateway->created_by = auth()->id();
        $gateway->save();

        if (!$gateway) {
            throw FailureException::serverError();
        }
    }

    public static function first($gateway = "stripe", $bypass = false)
    {
        $gateway = PaymentGateway::with(['agency'])
            ->where('gateway', clean($gateway))
            ->where('agency_id', app('agency')->id)
            ->first();

        if (!$gateway && !$bypass) {
            throw ModelException::dataNotFound();
        }

        return $gateway;
    }

    public static function changeStatus(PaymentGateway $gateway)
    {
        $gateway->status = $gateway->is_enable ? false : true;
        $gateway->updated_by = auth()->id();
        $gateway->save();
    }
}
