<?php

namespace App\Http\Businesses\V1\Customer;

use App\Http\Services\V1\Customer\BillingInformationService;

class BillingInformationBusiness
{
    public static function store($request)
    {
        return BillingInformationService::store($request);
    }

    public static function first(int $id)
    {
        return BillingInformationService::first($id);
    }

    public static function update($request, int $id)
    {
        $billing = BillingInformationService::first($id);
        return BillingInformationService::update($request, $billing);
    }

    public static function destroy(int $id)
    {
        $billing = BillingInformationService::first($id);
        BillingInformationService::destroy($billing);
    }
}
