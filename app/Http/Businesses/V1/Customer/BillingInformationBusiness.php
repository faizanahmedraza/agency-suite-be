<?php

namespace App\Http\Businesses\V1\Customer;

use App\Http\Services\V1\Customer\BillingInformationService;

class BillingInformationBusiness
{
    public static function store($request)
    {
        return BillingInformationService::store($request);
    }

    public static function get()
    {
        return BillingInformationService::get();
    }

    public static function first($id)
    {
        return BillingInformationService::first($id,['agency','customer']);
    }

    public static function update($request,$id)
    {
        $billing = self::first($id);
        return BillingInformationService::update($request, $billing);
    }

    public static function destroy()
    {
        $billing = self::first();
        BillingInformationService::destroy($billing);
    }
}
