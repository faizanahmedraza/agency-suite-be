<?php

namespace App\Http\Businesses\V1\Customer;

use App\Exceptions\V1\RequestValidationException;
use App\Http\Services\V1\Customer\BillingInformationService;
use App\Http\Services\V1\Customer\TransactionService;
use App\Http\Services\V1\Customer\CustomerInvoiceService;
use App\Http\Services\V1\Customer\ServiceBusinessService;
use App\Http\Services\V1\Customer\CustomerServiceRequestService;
use App\Models\CustomerCardDetail;
use Illuminate\Http\Request;

class RequestServiceBusiness
{
    public static function requestService($request)
    {
        $customerBillingInfo = CustomerCardDetail::where('agency_id', app('agency')->id)->where('customer_id', \auth()->id())->first();
        if (empty($customerBillingInfo)) {
            throw RequestValidationException::errorMessage("Please add your billing info first.");
        }
        $data = $request->all();
        $service = ServiceBusinessService::first($data['service_id']);
        if ($service->subscription_type == 1 && !isset($data['recurring_type'])) {
            throw RequestValidationException::errorMessage("Recurring type is required.");
        }
        if ($service->subscription_type == 1) {
            $maxReq = $service->priceTypes->max_requests_per_month;
        } else {
            $maxReq = $service->priceTypes->purchase_limit;
        }
        $customerRequests = CustomerServiceRequestService::getByCustomer(['service_id' => $data['service_id']]);
        if (!is_null($maxReq) && count($customerRequests) == $maxReq) {
            throw RequestValidationException::errorMessage("Request limit reached.");
        }
        $customerServiceRequest = CustomerServiceRequestService::create($data, $service);
        $customerInvoice = CustomerInvoiceService::create($customerServiceRequest, $service);
        $trancsaction = TransactionService::create($customerInvoice, 'card');
    }

    public static function get($request)
    {
        return CustomerServiceRequestService::get($request);
    }

    public static function first($id)
    {
        return CustomerServiceRequestService::first($id);
    }

    public static function changeStatus($id,Request $request)
    {
        $requestService = self::first($id);
        CustomerServiceRequestService::changeStatus($requestService, $request);
    }
}
