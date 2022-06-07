<?php

namespace App\Http\Services\V1\Agency;

use App\Models\Service;
use Illuminate\Http\Request;
use App\Helpers\TimeStampHelper;

use App\Exceptions\V1\ModelException;
use App\Models\CustomerServiceRequest;
use App\Exceptions\V1\FailureException;

class PaymentGatewayService
{
    public static function create(Request $request, $service)
    {
        $customerServiceRequest = new CustomerServiceRequest();
        $customerServiceRequest->agency_id = app('agency')->id;
        $customerServiceRequest->customer_id = $data['customer_id'];
        $customerServiceRequest->service_id = $data['service_id'];
        $customerServiceRequest->is_recurring = $service->subscription_type;
        $customerServiceRequest->status = CustomerServiceRequest::STATUS['pending'];
        $customerServiceRequest->intake_form = json_encode($data['intake_form']);
        $customerServiceRequest->created_by = auth()->id();
        if ($service->subscription_type == 1) {
            $customerServiceRequest->recurring_type = $data['recurring_type'];
        }
        $customerServiceRequest->save();

        if (!$customerServiceRequest) {
            throw FailureException::serverError();
        }

        return $customerServiceRequest;
    }

    public static function first(Request $request,$gateway = null,$bypass = false)
    {
        $gateway = CustomerServiceRequest::with($with)
            ->where('id', $id)
            ->where('agency_id', app('agency')->id)
            ->first();

        if (!$gateway && !$bypass) {
            throw ModelException::dataNotFound();
        }

        return $gateway;
    }
}
