<?php

namespace App\Http\Businesses\V1\Agency;

use App\Exceptions\V1\RequestValidationException;
use App\Http\Services\V1\Agency\AgencyBusinessService;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ServiceBusiness
{
    public static function store(Request $request)
    {
        if ($request->has('image') && !empty($request->image) && !validate_base64($request->image, ['png', 'jpg', 'jpeg'])) {
            throw RequestValidationException::errorMessage('Invalid image. Base64 image string is required. Allowed formats are png,jpg,jpeg.');
        }
        if (Service::SUBSCRIPTION_TYPES[clean($request->subscription_type)] == Service::SUBSCRIPTION_TYPES['recurring'] && (empty($request->weekly) && empty($request->monthly) && empty($request->quarterly) && empty($request->biannually) && empty($request->annually))) {
            throw RequestValidationException::errorMessage('Please add atleast one recurring type.');
        }
        // create agency service
        return AgencyBusinessService::create($request);
    }

    public static function update(Request $request, $id)
    {
        if ($request->has('image') && !empty($request->image) && !Str::contains($request->image, ['https', 'cloudinary']) && !validate_base64($request->image, ['png', 'jpg', 'jpeg'])) {
            throw RequestValidationException::errorMessage('Invalid image. Base64 image string is required. Allowed formats are png,jpg,jpeg.');
        }
        if (Service::SUBSCRIPTION_TYPES[clean($request->subscription_type)] == Service::SUBSCRIPTION_TYPES['recurring'] && (empty($request->weekly) && empty($request->monthly) && empty($request->quarterly) && empty($request->biannually) && empty($request->annually))) {
            throw RequestValidationException::errorMessage('Please add atleast one recurring type.');
        }
        //get service
        $service = AgencyBusinessService::first($id);
        // create agency service
        return AgencyBusinessService::update($service, $request);
    }

    public static function first($id)
    {
        return AgencyBusinessService::first($id);
    }

    public static function get(Request $request)
    {
        return AgencyBusinessService::get($request);
    }

    public static function destroy($id)
    {
        $service = AgencyBusinessService::first($id);
        AgencyBusinessService::destroy($service);
    }

    public static function toggleCatalogStatus($id)
    {
        // get service
        $service = AgencyBusinessService::first($id);
        // catalog status toggle
        AgencyBusinessService::toggleCatalogStatus($service);
    }

    public static function toggleStatus($id, Request $request)
    {
        // get service
        $service = AgencyBusinessService::first($id);
        // status toggle
        AgencyBusinessService::toggleStatus($service, $request);
    }
}
