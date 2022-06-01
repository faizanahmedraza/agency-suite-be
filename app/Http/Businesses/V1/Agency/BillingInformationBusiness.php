<?php

namespace App\Http\Businesses\V1\Agency;

use App\Http\Services\V1\Agency\BillingInformationService;
use Illuminate\Http\Request;

class BillingInformationBusiness
{
    public static function first($id)
    {
        return BillingInformationService::first($id, ['agency', 'customer']);
    }

    public static function get(Request $request)
    {
        return BillingInformationService::get($request);
    }
}
