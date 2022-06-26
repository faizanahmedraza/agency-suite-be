<?php

namespace App\Http\Services\V1\Agency;

use App\Models\Service;
use Illuminate\Http\Request;
use App\Helpers\TimeStampHelper;

use App\Exceptions\V1\ModelException;
use App\Models\CustomerServiceRequest;
use App\Exceptions\V1\FailureException;

class CustomerServiceRequestService
{
    public static function create(array $data, $service)
    {
        $customerServiceRequest = new CustomerServiceRequest();
        $customerServiceRequest->agency_id = app('agency')->id;
        $customerServiceRequest->customer_id = $data['customer_id'];
        $customerServiceRequest->service_id = $data['service_id'];
        $customerServiceRequest->is_recurring = $service->subscription_type;
        $customerServiceRequest->quantity = $data['quantity'];
        $customerServiceRequest->status = CustomerServiceRequest::STATUS['pending'];
        $customerServiceRequest->intake_form = json_encode($data['intake_form']);
        $customerServiceRequest->created_by = auth()->id();
        if ($service->subscription_type == 1) {
            $customerServiceRequest->recurring_type = $data['recurring_type'];
            $customerServiceRequest->next_recurring_date = recurringInvoiceDate($customerServiceRequest->recurring_type);
        }
        $customerServiceRequest->save();

        if (!$customerServiceRequest) {
            throw FailureException::serverError();
        }

        return $customerServiceRequest;
    }

    public static function first(int $id, $with = ['agency', 'customer', 'service', 'service.priceTypes'])
    {
        $customerServiceRequest = CustomerServiceRequest::with($with)
            ->where('id', $id)
            ->where('agency_id', app('agency')->id)
            ->first();

        if (!$customerServiceRequest) {
            throw ModelException::dataNotFound();
        }

        return $customerServiceRequest;
    }

    public static function get(Request $request)
    {
        $customerServiceRequest = CustomerServiceRequest::query()->where('agency_id', app('agency')->id);

        if ($request->query('title')) {
            $arrTitleIds = Service::where('name', 'like', '%' . $request->query('title') . '%')->pluck('id');
            $customerServiceRequest->whereIn('service_id', $arrTitleIds);
        }
        if ($request->query('customer_id')) {
            $customerServiceRequest->where('customer_id', $request->query('customer_id'));
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

    public static function getByCustomer($id, $where = [])
    {
        $service = CustomerServiceRequest::where('customer_id', $id)
            ->where('agency_id', app('agency')->id)->where($where)->get();

        if (!$service) {
            throw ModelException::dataNotFound();
        }

        return $service;
    }

    public static function changeStatus(CustomerServiceRequest $requestService, Request $request)
    {
        $requestService->status = CustomerServiceRequest::STATUS[trim(strtolower($request->status))];
        $requestService->updated_by = auth()->id();
        $requestService->save();
    }

    public static function getRecurringServiceRequests(Request $request)
    {
        return CustomerServiceRequest::where('is_recurring', true)->where('status', CustomerServiceRequest::STATUS[clean($request->status)])->get();
    }

    public static function UpdateRecurringDateWhere($id, $next_recurring_date)
    {
        CustomerServiceRequest::where('id', $id)->update([
            "next_recurring_date" => $next_recurring_date
        ]);
    }
}
