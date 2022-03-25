<?php

namespace App\Http\Services\V1\Customer;

use App\Exceptions\V1\ModelException;
use App\Exceptions\V1\FailureException;
use App\Models\CustomerBillingInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillingInformationService
{
    public static function store(Request $request)
    {
        $billing = CustomerBillingInformation::where('agency_id', app('agency')->id)->where('customer_id', Auth::id())->first();

        if (empty($billing)) {
            $billing = new CustomerBillingInformation();
            $billing->agency_id = app('agency')->id;
            $billing->customer_id = Auth::id();
        }

        $billing->invoice_to = trim($request->invoice_to);
        $billing->country = trim($request->country);
        $billing->address = trim($request->address);
        $billing->city = trim($request->city);
        $billing->state = trim($request->state);
        $billing->zip_code = trim($request->zip_code);
        $billing->tax_code = trim($request->tax_code);
        $billing->save();

        if (!$billing) {
            throw FailureException::serverError();
        }

        return $billing;
    }

    public static function first($with = ['customer', 'agency'])
    {
        $billing = CustomerBillingInformation::with($with)->where('agency_id', app('agency')->id)->where('customer_id', Auth::id())->first();

        if (!$billing) {
            throw ModelException::dataNotFound();
        }

        return $billing;
    }

    public static function update(Request $request, CustomerBillingInformation $billing)
    {
        $billing->invoice_to = trim($request->invoice_to);
        $billing->country = trim($request->country);
        $billing->address = trim($request->address);
        $billing->city = trim($request->city);
        $billing->state = trim($request->state);
        $billing->zip_code = trim($request->zip_code);
        $billing->tax_code = trim($request->tax_code);
        $billing->save();

        if (!$billing) {
            throw FailureException::serverError();
        }

        return $billing;
    }

    public static function destroy(CustomerBillingInformation $billing)
    {
        $billing->delete();
    }
}
