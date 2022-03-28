<?php

namespace App\Http\Businesses\V1\Customer;

use App\Http\Services\V1\Customer\BillingInformationService;

class BillingInformationBusiness
{
    public static function store($request)
    {
        return BillingInformationService::store($request);
    }

    public static function first()
    {
        return BillingInformationService::first();
    }

    public static function update($request)
    {
        $billing = BillingInformationService::first();
        return BillingInformationService::update($request, $billing);
    }

    public static function destroy()
    {
        $billing = BillingInformationService::first();
        BillingInformationService::destroy($billing);
    }
}
