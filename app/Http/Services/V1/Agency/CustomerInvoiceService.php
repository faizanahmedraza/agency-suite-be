<?php

namespace App\Http\Services\V1\Agency;

use Illuminate\Http\Request;
use App\Models\CustomerInvoice;

use App\Helpers\TimeStampHelper;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\V1\ModelException;
use App\Exceptions\V1\FailureException;

class CustomerInvoiceService
{
    public static function create($data,$service)
    {
        $type=$data->recurring_type;
        $customerInvoice = new CustomerInvoice();
        $customerInvoice->agency_id = app('agency')->id;
        $customerInvoice->customer_service_request_id =$data->id;
        $customerInvoice->amount = $service->priceTypes->price;
        if($service->subscription_type == 1){
            $customerInvoice->amount = $service->priceTypes->$type;
        }
        $customerInvoice->customer_id = $data->customer_id;
        $customerInvoice->is_paid = 0;
        $customerInvoice->save();

        if (!$customerInvoice) {
            throw FailureException::serverError();
        }

        return $customerInvoice;
    }



}
