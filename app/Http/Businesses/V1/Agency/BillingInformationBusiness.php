<?php

namespace App\Http\Businesses\V1\Agency;

use App\Http\Services\V1\Agency\BillingInformationService;

class BillingInformationBusiness
{
    public static function first($id)
    {
        return BillingInformationService::first($id, ['agency', 'customer']);
    }
}
