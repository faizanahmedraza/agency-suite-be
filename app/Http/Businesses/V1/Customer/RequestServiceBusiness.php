<?php

namespace App\Http\Businesses\V1\Customer;

use App\Exceptions\V1\RequestValidationException;
use App\Http\Services\V1\Customer\BillingInformationService;
use App\Http\Services\V1\Customer\TransactionService;
use App\Http\Services\V1\Customer\CustomerInvoiceService;
use App\Http\Services\V1\Customer\ServiceBusinessService;
use App\Http\Services\V1\Customer\CustomerServiceRequestService;
use App\Models\CustomerCardDetail;
use App\Models\CustomerServiceRequest;
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

        self::concurrentRequests($service, $data);

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

    public static function changeStatus($id, Request $request)
    {
        $requestService = self::first($id);
        CustomerServiceRequestService::changeStatus($requestService, $request);
    }

    public static function cancelRequest($id)
    {
        $requestService = self::first($id);
        if ($requestService->status == CustomerServiceRequest::STATUS['completed']) {
            throw RequestValidationException::errorMessage('This service cannot be cancelled. It is already completed.');
        }
        CustomerServiceRequestService::cancelRequest($requestService);
    }

    public static function concurrentRequests($service, $data)
    {
        $maxMonthReq = null;
        $customerMonthRequests = null;
        $customerRequests = null;
        if ($service->subscription_type == 1) {
            $maxMonthReq = $service->priceTypes->max_requests_per_month;
            $maxReq = $service->priceTypes->max_concurrent_requests;
        } else {
            $maxReq = $service->priceTypes->purchase_limit;
        }
        if (!is_null($maxMonthReq)) {
            $customerMonthRequests = CustomerServiceRequestService::getCustomerByRequests($data['service_id'], date('m'));
        }
        $customerRequests = CustomerServiceRequestService::getCustomerByRequests($data['service_id'], null, date('Y'));
        if ((!is_null($maxMonthReq) && $customerMonthRequests == $maxMonthReq) || (!is_null($maxReq) && $customerRequests == $maxReq)) {
            throw RequestValidationException::errorMessage("Request limit reached.");
        }
    }
}
