<?php

namespace App\Http\Businesses\V1\Agency;

use App\Exceptions\V1\RequestValidationException;
use App\Exceptions\V1\ServerException;
use App\Http\Services\V1\Agency\AgencyBusinessService;
use App\Http\Services\V1\Agency\CustomerInvoiceService;
use App\Http\Services\V1\Agency\CustomerService;
use App\Http\Services\V1\Agency\CustomerServiceRequestService;
use App\Http\Services\V1\Agency\TransactionService;
use App\Models\CustomerServiceRequest;
use Illuminate\Http\Request;

class RequestServiceBusiness
{
    public static function requestService(Request $request)
    {
        $data = $request->all();

//        $customerBillingInfo = BillingInformationService::first($data['customer_id'],[],true);
//        if($customerBillingInfo == null){
//            throw RequestValidationException::errorMessage("Please add customer billing info first.");
//        }

        $service = AgencyBusinessService::first($data['service_id']);

        $customer = CustomerService::first($data['customer_id']);

        if ($customer->status == 2) {
            throw RequestValidationException::errorMessage('The customer you selected is blocked, if you would like quicker access please contact support', 422);
        }

        if ($service->status == 2) {
            throw ServerException::errorMessage('The service you selected is blocked, if you would like quicker access please contact support', 422);
        }

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

    public static function changeStatus(Request $request, $id)
    {
        $requestService = self::first($id);
        if ($requestService->status == CustomerServiceRequest::STATUS['completed']) {
            throw RequestValidationException::errorMessage('This service cannot be cancelled. It is already completed.');
        }
        CustomerServiceRequestService::changeStatus($requestService, $request);
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
            $customerMonthRequests = CustomerServiceRequestService::getCustomerByRequests($data['customer_id'], $data['service_id'], date('m'));
        }
        $customerRequests = CustomerServiceRequestService::getCustomerByRequests($data['customer_id'], $data['service_id'], null, date('Y'));
        if ((!is_null($maxMonthReq) && $customerMonthRequests == $maxMonthReq) || (!is_null($maxReq) && $customerRequests == $maxReq)) {
            throw RequestValidationException::errorMessage("Request limit reached.");
        }
    }
}
