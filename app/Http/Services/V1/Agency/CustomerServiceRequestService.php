<?php

namespace App\Http\Services\V1\Agency;

use App\Models\Service;
use Illuminate\Http\Request;
use App\Helpers\TimeStampHelper;

use Illuminate\Support\Facades\Auth;
use App\Exceptions\V1\ModelException;
use App\Models\CustomerServiceRequest;
use App\Exceptions\V1\FailureException;

class CustomerServiceRequestService
{
    public static function first(int $id, $with = ['agency', 'customer','service'])
    {
        $service = CustomerServiceRequest::with($with)
            ->where('id', $id)
            ->where('agency_id', app('agency')->id)
            ->first();

        if (!$service) {
            throw ModelException::dataNotFound();
        }

        return $service;
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
            $arrStatus = getStatus(CustomerServiceRequest::STATUS, clean($request->status));
            $customerServiceRequest->whereIn('status', $arrStatus);
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

}
