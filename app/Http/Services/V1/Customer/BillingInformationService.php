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
        $billing = new CustomerBillingInformation();
        $billing->invoice_to = $request->invoice_to;
        $billing->country = $request->country;
        $billing->address = $request->address;
        $billing->city = $request->city;
        $billing->state = $request->state;
        $billing->zip_code = $request->zip_code;
        $billing->tax_code = $request->tax_code;
        $billing->agency_id = app('agency')->id;
        $billing->customer_id = Auth::id();
        $billing->save();

        if (!$billing) {
            throw FailureException::serverError();
        }

        return $billing;
    }

    public static function first($id,$with = ['customer','agency'])
    {
        $billing = CustomerBillingInformation::with($with)->where('agency_id',app('agency')->id)->where('customer_id',Auth::id())->find($id);

        if (!$billing) {
            throw ModelException::dataNotFound();
        }

        return $billing;
    }

    public static function update(Request $request, CustomerBillingInformation $billing)
    {
        $billing->invoice_to = $request->invoice_to;
        $billing->country = $request->country;
        $billing->address = $request->address;
        $billing->city = $request->city;
        $billing->state = $request->state;
        $billing->zip_code = $request->zip_code;
        $billing->tax_code = $request->tax_code;
        $billing->save();

        if (!$billing) {
            throw FailureException::serverError();
        }

        return $billing;
    }

    public static function destroy(CustomerBillingInformation $billing): void
    {
        $billing->delete();
    }
}
