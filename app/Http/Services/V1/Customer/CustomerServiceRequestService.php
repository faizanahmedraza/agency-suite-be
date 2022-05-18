<?php

namespace App\Http\Services\V1\Customer;

use App\Models\Service;
use Illuminate\Http\Request;
use App\Helpers\TimeStampHelper;

use Illuminate\Support\Facades\Auth;
use App\Exceptions\V1\ModelException;
use App\Models\CustomerServiceRequest;
use App\Exceptions\V1\FailureException;

class CustomerServiceRequestService
{
    public static function create(array $data,$service)
    {
        $customerServiceRequest = new CustomerServiceRequest();
        $customerServiceRequest->agency_id = app('agency')->id;
        $customerServiceRequest->customer_id = Auth::id();
        $customerServiceRequest->service_id = $data['service_id'];
        $customerServiceRequest->is_recurring = $service->subscription_type;
        $customerServiceRequest->status = CustomerServiceRequest::STATUS['pending'];
        $customerServiceRequest->intake_form = json_encode($data['intake_form']);
        if($service->subscription_type == 1){
            $customerServiceRequest->recurring_type=$data['recurring_type'];
        }
        $customerServiceRequest->save();

        if (!$customerServiceRequest) {
            throw FailureException::serverError();
        }

        return $customerServiceRequest;
    }

    public static function first(int $id, $with = ['agency', 'customer','service','service.priceTypes'])
    {
        $service = CustomerServiceRequest::with($with)
            ->where('id', $id)
            ->where('customer_id', Auth::id())
            ->where('agency_id', app('agency')->id)
            ->first();

        if (!$service) {
            throw ModelException::dataNotFound();
        }

        return $service;
    }

    public static function get(Request $request)
    {
        $customerServiceRequest = CustomerServiceRequest::query()->with(['service','customer','customer.user'])->where('customer_id', Auth::id())->where('agency_id', app('agency')->id);

        if ($request->query('title')) {
            $arrTitleIds = Service::where('name', 'like', '%' . $request->query('title') . '%')->pluck('id');
            $customerServiceRequest->whereIn('service_id', $arrTitleIds);
        }
        if ($request->query('status')) {
            $customerServiceRequest->where('status', CustomerServiceRequest::STATUS[clean($request->status)]);
        }

        if ($request->query('order_by')) {
            $customerServiceRequest->orderBy('id', $request->get('order_by'));
        } else {
            $customerServiceRequest->orderBy('id', 'desc');
        }

        if ($request->query('from_date')) {
            $from = TimeStampHelper::formateDate($request->from_date);
            $customerServiceRequest->whereDate('created_at', '>=', $from);
        }

        if ($request->query('to_date')) {
            $to = TimeStampHelper::formateDate($request->to_date);
            $customerServiceRequest->whereDate('created_at', '<=', $to);
        }

        return ($request->filled('pagination') && $request->get('pagination') == 'false')
            ? $customerServiceRequest->get()
            : $customerServiceRequest->paginate(\pageLimit($request));

    }

    public static function getByCustomer($where=[])
    {
        $service = CustomerServiceRequest::where('customer_id', Auth::id())
                                          ->where('agency_id', app('agency')->id)->where($where)->get();

        if (!$service) {
            throw ModelException::dataNotFound();
        }

        return $service;
    }

}
