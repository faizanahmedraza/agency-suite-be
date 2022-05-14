<?php

namespace App\Http\Businesses\V1\Customer;

use App\Http\Services\V1\Customer\BillingInformationService;

class BillingInformationBusiness
{
    public static function store($request)
    {
        $billing = self::first();
        return BillingInformationService::store($request,$billing);
    }

    public static function first()
    {
        return BillingInformationService::first(auth()->id(),['agency','customer'],true);
    }

    public static function update($request)
    {
        $billing = self::first();
        return BillingInformationService::update($request, $billing);
    }

    public static function destroy()
    {
        $billing = self::first();
        BillingInformationService::destroy($billing);
    }
}
