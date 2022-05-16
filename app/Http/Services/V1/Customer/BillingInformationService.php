<?php

namespace App\Http\Services\V1\Customer;

use App\Exceptions\V1\ModelException;
use App\Exceptions\V1\FailureException;
use App\Models\CustomerCardDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillingInformationService
{
    public static function get()
    {
        return CustomerCardDetail::with(['customer', 'agency'])->where('agency_id', app('agency')->id)->where('customer_id', \auth()->id())->get();
    }

    public static function store(Request $request)
    {
        $billing = new CustomerCardDetail;
        $billing->agency_id = app('agency')->id;
        $billing->customer_id = Auth::id();

        $billing->holder_name = trim($request->holder_name);
        $billing->last_digits = trim(substr($request->card_no, -4));
        $billing->cvc = trim($request->cvc);
        $billing->exp_month = trim($request->expiry_month);
        $billing->exp_year = trim($request->expiry_year);
        $billing->address = trim($request->address);
        $billing->country = trim($request->country);
        $billing->city = trim($request->city);
        $billing->state = trim($request->state);
        $billing->street = trim($request->street);
        $billing->zip_code = trim($request->zip_code);
        $billing->save();

        if (!$billing) {
            throw FailureException::serverError();
        }

        return $billing;
    }

    public static function first($id, $with = ['customer', 'agency'], $bypass = false)
    {
        return CustomerCardDetail::with($with)->where('agency_id', app('agency')->id)->where('customer_id', $id)->first();
    }

    public static function update(Request $request, CustomerCardDetail $billing)
    {
        $billing->holder_name = trim($request->holder_name);
        $billing->last_digits = trim(substr($request->card_no, -4));
        $billing->cvc = trim($request->cvc);
        $billing->expiry_month = trim($request->expiry_month);
        $billing->expiry_year = trim($request->expiry_year);
        $billing->address = trim($request->address);
        $billing->country = trim($request->country);
        $billing->city = trim($request->city);
        $billing->state = trim($request->state);
        $billing->street = trim($request->street);
        $billing->zip_code = trim($request->zip_code);
        $billing->save();

        if (!$billing) {
            throw FailureException::serverError();
        }

        return $billing;
    }

    public static function destroy(CustomerCardDetail $billing)
    {
        $billing->delete();
    }
}
