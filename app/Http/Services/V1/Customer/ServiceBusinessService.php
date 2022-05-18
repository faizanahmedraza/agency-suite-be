<?php

namespace App\Http\Services\V1\Customer;

use App\Exceptions\V1\ModelException;
use App\Helpers\TimeStampHelper;
use App\Models\Service;

use function Clue\StreamFilter\fun;
use Illuminate\Http\Request;

class ServiceBusinessService
{
    public static function first(int $id, $with = ['intakes', 'priceTypes'])
    {
        $service = Service::with($with)
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
        $services = Service::query()->with(['intakes', 'priceTypes']);

        if ($request->query("services")) {
            $ids = \getIds($request->services);
            $services->orWhereIn('id', $ids);
        }

        if ($request->query('name')) {
            $services->whereRaw("TRIM(LOWER(name)) = ? ", trim(strtolower($request->name)));
        }

        if ($request->query('service_type')) {
            $services->where('subscription_type',trim(clean($request->service_type)));
        }

        if ($request->query('order_by')) {
            $services->orderBy('id', $request->get('order_by'));
        } else {
            $services->orderBy('id', 'desc');
        }

        if ($request->query('from_date')) {
            $from = TimeStampHelper::formateDate($request->from_date);
            $services->whereDate('created_at', '>=', $from);
        }

        if ($request->query('to_date')) {
            $to = TimeStampHelper::formateDate($request->to_date);
            $services->whereDate('created_at', '<=', $to);
        }

        $services->where('agency_id', app('agency')->id)->where('catalog_status', 1)->where('status', 1);

        return ($request->filled('pagination') && $request->get('pagination') == 'false')
            ? $services->get()
            : $services->paginate(\pageLimit($request));
    }


}
