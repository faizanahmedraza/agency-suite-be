<?php

namespace App\Http\Businesses\V1\Agency;

use App\Exceptions\V1\RequestValidationException;
use App\Http\Services\V1\Agency\AgencyBusinessService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ServiceBusiness
{
    public static function store(Request $request)
    {
        if ($request->has('image') && !empty($request->image) && !validate_base64($request->image, ['png', 'jpg', 'jpeg'])) {
            throw RequestValidationException::errorMessage('Invalid image. Base64 image string is required. Allowed formats are png,jpg,jpeg.');
        }
        // create agency service
        return AgencyBusinessService::create($request);
    }

    public static function update(Request $request, $id)
    {
        if ($request->has('image') && !empty($request->image) && !Str::contains($request->image, ['res', 'https', 'cloudinary']) && !validate_base64($request->image, ['png', 'jpg', 'jpeg'])) {
            throw RequestValidationException::errorMessage('Invalid image. Base64 image string is required. Allowed formats are png,jpg,jpeg.');
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

    public static function toggleStatus($id)
    {
        // get user
        $service = AgencyBusinessService::first($id);
        // status toggle
        AgencyBusinessService::toggleStatus($service);
    }
}
