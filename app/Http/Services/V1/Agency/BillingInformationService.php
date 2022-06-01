<?php

namespace App\Http\Services\V1\Agency;

use App\Exceptions\V1\ModelException;
use App\Exceptions\V1\FailureException;
use App\Models\CustomerCardDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillingInformationService
{
    public static function first($id, $with = ['customer', 'agency'])
    {
        $billingInfo = CustomerCardDetail::with($with)->where('id', $id)->where('agency_id', app('agency')->id)->first();
        if (!$billingInfo) {
            throw ModelException::dataNotFound();
        }
        return $billingInfo;
    }
}
