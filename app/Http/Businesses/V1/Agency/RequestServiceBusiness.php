<?php

namespace App\Http\Businesses\V1\Agency;

use App\Exceptions\V1\RequestValidationException;
use App\Http\Services\V1\Agency\AgencyBusinessService;
use App\Http\Services\V1\Agency\BillingInformationService;
use App\Http\Services\V1\Agency\CustomerInvoiceService;
use App\Http\Services\V1\Agency\CustomerServiceRequestService;
use App\Http\Services\V1\Agency\TransactionService;
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

        $service = AgencyBusinessService::first($data['service_id'], []);

        if ($service->subscription_type == 1 && !isset($data['recurring_type'])) {
            throw RequestValidationException::errorMessage("Recurring type is required.");
        }

        if ($service->subscription_type == 1) {
            $maxReq = $service->priceTypes->max_requests_per_month;
        } else {
            $maxReq = $service->priceTypes->purchase_limit;
        }

        $customerRequests = CustomerServiceRequestService::getByCustomer($data['customer_id'], ['service_id' => $data['service_id']]);

        if (count($customerRequests) == $maxReq) {
            throw RequestValidationException::errorMessage("Request limit reached.");
        }

        $customerServiceRequest = CustomerServiceRequestService::create($data, $service);

        $customerInvoice = CustomerInvoiceService::create($customerServiceRequest, $service);

        $trancsaction = TransactionService::create($customerInvoice, $service);
    }

    public static function get($request)
    {
        return CustomerServiceRequestService::get($request);
    }

    public static function first($id)
    {
        return CustomerServiceRequestService::first($id, []);
    }

    public static function changeStatus(Request $request, $id)
    {
        $requestService = self::first($id);
        CustomerServiceRequestService::changeStatus($requestService,$request);
    }
}
