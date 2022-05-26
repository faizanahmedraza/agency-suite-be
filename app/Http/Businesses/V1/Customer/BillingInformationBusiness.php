<?php

namespace App\Http\Businesses\V1\Customer;

use App\Exceptions\V1\RequestValidationException;
use App\Http\Services\V1\Customer\BillingInformationService;
use Carbon\Carbon;

class BillingInformationBusiness
{
    public static function store($request)
    {
        $date = Carbon::now();
        $month = $date->format('m');
        $year = $date->format('y');
        if ($request->expiry_month < $month && $request->expiry_year < $year) {
            throw RequestValidationException::errorMessage("Expiry date cann't be less than current date.");
        }
        return BillingInformationService::store($request);
    }

    public static function get()
    {
        return BillingInformationService::get();
    }

    public static function first($id)
    {
        return BillingInformationService::first($id, ['agency', 'customer']);
    }

    public static function update($request, $id)
    {
        $billing = self::first($id);
        return BillingInformationService::update($request, $billing);
    }

    public static function destroy($id)
    {
        $billing = self::first($id);
        BillingInformationService::destroy($billing);
    }

    public static function makePrimary($id)
    {
        $billing = self::first($id);
        BillingInformationService::makePrimary($billing);
    }
}
