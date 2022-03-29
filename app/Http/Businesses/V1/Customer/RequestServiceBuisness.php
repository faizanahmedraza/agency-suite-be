<?php

namespace App\Http\Businesses\V1\Customer;

use App\Exceptions\V1\RequestValidationException;
use App\Http\Services\V1\Customer\TransactionService;
use App\Http\Services\V1\Customer\CustomerInvoiceService;
use App\Http\Services\V1\Customer\ServiceBusinessService;
use App\Http\Services\V1\Customer\CustomerServiceRequestService;
use App\Http\Services\V1\Customer\BillingInformationService;

class RequestServiceBuisness
{

    public static function requestService($request)
    {
        $customerBillingInfo=BillingInformationService::first([],true);
        if($customerBillingInfo == null){
            throw RequestValidationException::errorMessage("Please add your billing info first.");
        }
        $maxReq=0;
        $data=$request->all();
        $service=ServiceBusinessService::first($data['service_id'],[]);
        if($service->subscription_type == 1 && !isset($data['recurring_type'])){
           throw RequestValidationException::errorMessage("Recurring type is required.");
        }
        if($service->subscription_type == 1){
            $maxReq=$service->priceTypes->max_requests_per_month;
        }else{
            $maxReq=$service->priceTypes->purchase_limit;
        }
        $customerRequests=CustomerServiceRequestService::getByCustomer(['service_id'=>$data['service_id']]);
        if(count($customerRequests) == $maxReq) {
            throw RequestValidationException::errorMessage("Request limit reached.");
        }
        $customerServiceRequest=CustomerServiceRequestService::create($data,$service);
        $customerInvoice=CustomerInvoiceService::create($customerServiceRequest,$service);
        $customerInvoice->refrence_no=$data['refrence_no'];
        $trancsaction=TransactionService::create($customerInvoice,$service);
    }

    public static function get($request)
    {
        return CustomerServiceRequestService::get($request);
    }


    public static function first($id)
    {
        return CustomerServiceRequestService::first($id,[]);
    }

}