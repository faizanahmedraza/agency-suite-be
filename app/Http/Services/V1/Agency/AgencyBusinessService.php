<?php

namespace App\Http\Services\V1\Agency;

use App\Exceptions\V1\ModelException;
use App\Helpers\TimeStampHelper;
use App\Http\Services\V1\CloudinaryService;
use App\Models\Service;
use App\Models\ServiceIntake;
use App\Models\ServicePriceType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AgencyBusinessService
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

    public static function create(Request $request)
    {
        $service = new Service();
        $service->name = $request->name;
        $service->description = $request->description;
        if ($request->has('image') && !empty($request->image)) {
            $service->image = CloudinaryService::upload($request->image)->secureUrl;
        }
        $service->subscription_type = Service::SUBSCRIPTION_TYPES[$request->subscription_type];
        $service->status = Service::STATUS['pending'];
        $service->agency_id = app('agency')->id;
        $service->save();

        $serviceIntake = new ServiceIntake();
        $serviceIntake->intake = json_encode(['title', 'description']);
        $service->agency_id = app('agency')->id;
        $service->intakes()->save($serviceIntake);

        $servicePriceType = new ServicePriceType();
        if (Service::SUBSCRIPTION_TYPES[$request->subscription_type] === 0) {
            $servicePriceType->price = $request->price;
            $servicePriceType->purchase_limit = $request->purchase_limit;
        } else {
            $servicePriceType->weekly = $request->weekly;
            $servicePriceType->monthly = $request->monthly;
            $servicePriceType->quarterly = $request->quarterly;
            $servicePriceType->biannually = $request->biannually;
            $servicePriceType->annually = $request->annually;
            $servicePriceType->max_concurrent_requests = $request->max_concurrent_requests;
            $servicePriceType->max_requests_per_month = $request->max_requests_per_month;
        }
        $service->agency_id = app('agency')->id;
        $service->priceTypes()->save($servicePriceType);

        return $service->refresh();
    }

    public static function update(Service $service, Request $request)
    {
        if ($request->has('image') && !empty($request->image) && !Str::contains($request->image, ['res', 'https', 'cloudinary'])) {
            $service->image = CloudinaryService::upload($request->image)->secureUrl;
        }
        $service->name = $request->name;
        $service->description = $request->description;
        $service->subscription_type = Service::SUBSCRIPTION_TYPES[$request->subscription_type];
        $service->status = Service::STATUS['pending'];
        $service->agency_id = app('agency')->id;
        $service->save();

        $serviceIntake = ServiceIntake::where('service_id', $service->id)->first();
        $serviceIntake->intake = json_encode(['title', 'description']);
        $service->agency_id = app('agency')->id;
        $service->intakes()->save($serviceIntake);

        $servicePriceType = ServicePriceType::where('service_id', $service->id)->first();
        if (Service::SUBSCRIPTION_TYPES[$request->subscription_type] === 0) {
            $servicePriceType->price = $request->price;
            $servicePriceType->purchase_limit = $request->purchase_limit;
        } else {
            $servicePriceType->weekly = $request->weekly;
            $servicePriceType->monthly = $request->monthly;
            $servicePriceType->quarterly = $request->quarterly;
            $servicePriceType->biannually = $request->biannually;
            $servicePriceType->annually = $request->annually;
            $servicePriceType->max_concurrent_requests = $request->max_concurrent_requests;
            $servicePriceType->max_requests_per_month = $request->max_requests_per_month;
        }
        $service->agency_id = app('agency')->id;
        $service->priceTypes()->save($servicePriceType);

        return $service->refresh();
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

        if ($request->query('status')) {
            $arrStatus = getStatus(Service::STATUS, clean($request->status));
            $services->wherein('status', $arrStatus);
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

        return ($request->filled('pagination') && $request->get('pagination') == 'false')
            ? $services->get()
            : $services->paginate(\pageLimit($request));
    }

    public static function destroy(Service $service)
    {
        $service->delete();
    }

}
