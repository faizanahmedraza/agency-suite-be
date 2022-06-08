<?php

namespace App\Http\Services\V1\Agency;

use App\Models\CustomerPaymentGateway;
use Illuminate\Http\Request;

use App\Exceptions\V1\ModelException;
use App\Exceptions\V1\FailureException;

class CustomerPaymentGatewayService
{
    public static function create(Request $request)
    {
        $customer_id = empty($request->customer_id) ? auth()->id() : $request->customer_id;
        $customer = new CustomerPaymentGateway();
        $customer->customer_id = $customer_id;
        $customer->agency_id = app('agency')->id;
        $customer->payment_gateway_id = $request->payment_gateway_id;
        $customer->customer_key = $request->customer_key;
        $customer->default = $request->default;
        $customer->created_by = auth()->id();
        $customer->save();

        if (!$customer) {
            throw FailureException::serverError();
        }
    }

    public static function first($customer_id,$gateway_id)
    {
        $customer = CustomerPaymentGateway::with(['agency', 'customer'])
            ->where('agency_id', app('agency')->id)
            ->where('customer_id', $customer_id)
            ->where('payment_gateway_id', $gateway_id)
            ->first();

        if (!$customer) {
            throw ModelException::dataNotFound();
        }

        return $customer;
    }
}
