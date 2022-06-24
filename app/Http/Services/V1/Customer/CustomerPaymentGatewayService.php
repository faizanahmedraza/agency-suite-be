<?php

namespace App\Http\Services\V1\Customer;

use App\Models\CustomerPaymentGateway;
use Illuminate\Http\Request;

use App\Exceptions\V1\ModelException;
use App\Exceptions\V1\FailureException;

class CustomerPaymentGatewayService
{
    public static function create(Request $request)
    {
        $customer = new CustomerPaymentGateway();
        $customer->customer_id = auth()->id();
        $customer->agency_id = app('agency')->id;
        $customer->payment_gateway_id = $request->payment_gateway_id;
        $customer->customer_key = $request->customer_key;
        $customer->default = $request->default;
        $customer->created_by = auth()->id();
        $customer->save();

        if (!$customer) {
            throw FailureException::serverError();
        }
        return $customer;
    }

    public static function first($gateway_id, $bypass = true)
    {
        $customer = CustomerPaymentGateway::with(['agency', 'customer'])
            ->where('agency_id', app('agency')->id)
            ->where('customer_id', auth()->id())
            ->where('payment_gateway_id', $gateway_id)
            ->first();

        if (!$customer && !$bypass) {
            throw ModelException::dataNotFound();
        }

        return $customer;
    }
}
