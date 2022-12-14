<?php

namespace App\Http\Services\V1\Agency;

use App\Exceptions\V1\ModelException;
use App\Exceptions\V1\RequestValidationException;
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
        $service->description = serialize($request->description);
        if ($request->has('image') && !empty($request->image)) {
            $service->image = CloudinaryService::upload($request->image)->secureUrl;
        }
        $service->subscription_type = Service::SUBSCRIPTION_TYPES[clean($request->subscription_type)];
        $service->catalog_status = Service::CATALOG_STATUS['pending'];
        $service->status = Service::STATUS['pending'];
        $service->agency_id = app('agency')->id;
        $service->save();

        $serviceIntake = new ServiceIntake();
        $serviceIntake->intake = json_encode([
            ['field' => 'text', 'name' => 'title'],
            ['field' => 'text', 'name' => 'description']
        ]);
        $serviceIntake->agency_id = app('agency')->id;
        $service->intakes()->save($serviceIntake);

        $servicePriceType = new ServicePriceType();
        if (Service::SUBSCRIPTION_TYPES[$request->subscription_type] === 0) {
            $servicePriceType->price = (int)$request->price;
            $servicePriceType->purchase_limit = !empty($request->purchase_limit) ? (int)$request->purchase_limit : null;
        } else {
            $servicePriceType->weekly = (int)$request->weekly;
            $servicePriceType->monthly = (int)$request->monthly;
            $servicePriceType->quarterly = (int)$request->quarterly;
            $servicePriceType->biannually = (int)$request->biannually;
            $servicePriceType->annually = (int)$request->annually;
            $servicePriceType->max_concurrent_requests = !empty($request->max_concurrent_requests) ? (int)$request->max_concurrent_requests : null;
            $servicePriceType->max_requests_per_month = !empty($request->max_requests_per_month) ? (int)$request->max_requests_per_month : null;
        }
        $servicePriceType->agency_id = app('agency')->id;
        $service->priceTypes()->save($servicePriceType);

        $service->load(['intakes', 'priceTypes']);
        return $service->refresh();
    }

    public static function update(Service $service, Request $request)
    {
        if ($request->has('image') && !empty($request->image) && !Str::contains($request->image, ['https', 'cloudinary'])) {
            $service->image = CloudinaryService::upload($request->image)->secureUrl;
        }
        $service->name = $request->name;
        $service->description = serialize($request->description);
        $service->subscription_type = Service::SUBSCRIPTION_TYPES[$request->subscription_type];
        $service->agency_id = app('agency')->id;
        $service->save();

        $serviceIntake = ServiceIntake::where('service_id', $service->id)->first();
        $serviceIntake->intake = json_encode([
            ['field' => 'text', 'name' => 'title'],
            ['field' => 'text', 'name' => 'description']
        ]);
        $serviceIntake->agency_id = app('agency')->id;
        $service->intakes()->save($serviceIntake);

        $servicePriceType = ServicePriceType::where('service_id', $service->id)->first();
        if (Service::SUBSCRIPTION_TYPES[$request->subscription_type] === 0) {
            $servicePriceType->price = (int)$request->price;
            $servicePriceType->purchase_limit = !empty($request->purchase_limit) ? (int)$request->purchase_limit : null;
            $servicePriceType->weekly = null;
            $servicePriceType->monthly = null;
            $servicePriceType->quarterly = null;
            $servicePriceType->biannually = null;
            $servicePriceType->annually = null;
            $servicePriceType->max_concurrent_requests = null;
            $servicePriceType->max_requests_per_month = null;
        } else {
            $servicePriceType->price = null;
            $servicePriceType->purchase_limit = null;
            $servicePriceType->weekly = (int)$request->weekly;
            $servicePriceType->monthly = (int)$request->monthly;
            $servicePriceType->quarterly = (int)$request->quarterly;
            $servicePriceType->biannually = (int)$request->biannually;
            $servicePriceType->annually = (int)$request->annually;
            $servicePriceType->max_concurrent_requests = !empty($request->max_concurrent_requests) ? (int)$request->max_concurrent_requests : null;
            $servicePriceType->max_requests_per_month = !empty($request->max_requests_per_month) ? (int)$request->max_requests_per_month : null;
        }
        $servicePriceType->agency_id = app('agency')->id;
        $service->priceTypes()->save($servicePriceType);

        $service->load(['intakes', 'priceTypes']);
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
            $services->whereRaw("TRIM(LOWER(name)) like ? ", '%' . trim(strtolower($request->name)) . '%');
        }

        if ($request->query('service_type')) {
            $services->where('subscription_type', Service::SUBSCRIPTION_TYPES[trim(clean($request->service_type))]);
        }

        if ($request->query('status')) {
            $statusAll = getStatus(Service::STATUS, trim(strtolower($request->status)));
            $services->whereIn('status', $statusAll);
        }

        if ($request->query('catalog_status')) {
            $services->where('catalog_status', Service::CATALOG_STATUS[clean($request->catalog_status)]);
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

        $services->where('agency_id', app('agency')->id);

        return ($request->filled('pagination') && $request->get('pagination') == 'false')
            ? $services->get()
            : $services->paginate(\pageLimit($request));
    }

    public static function destroy(Service $service)
    {
        if ($service->serviceRequests->isEmpty()) {
            $service->delete();
        } else {
            throw RequestValidationException::errorMessage("This service is requested by customers.", 422);
        }
    }

    public static function toggleCatalogStatus(Service $service)
    {
        ($service->catalog_status == Service::CATALOG_STATUS['pending']) ? $service->catalog_status = Service::CATALOG_STATUS['active'] : $service->catalog_status = Service::CATALOG_STATUS['pending'];
        $service->save();
    }

    public static function toggleStatus(Service $service,Request $request)
    {
        $service->status = Service::STATUS[trim(strtolower($request->status))];
        $service->save();
    }

    public static function getServiceById(int $id, $with = ['intakes', 'priceTypes'])
    {
        $service = Service::with($with)
            ->where('id', $id)
            ->first();

        if (!$service) {
            throw ModelException::dataNotFound();
        }

        return $service;
    }
}
