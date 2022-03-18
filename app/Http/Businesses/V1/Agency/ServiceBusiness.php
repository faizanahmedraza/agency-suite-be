<?php

namespace App\Http\Businesses\V1\Agency;

use App\Exceptions\V1\RequestValidationException;
use App\Http\Services\V1\Agency\AgencyBusinessService;
use Illuminate\Http\Request;

class ServiceBusiness
{
    public static function store(Request $request)
    {
        if (!validate_base64($request->image, ['png', 'jpg', 'jpeg'])) {
            throw RequestValidationException::errorMessage('Invalid image. Base64 image string is required. Allowed formats are png,jpg,jpeg.');
        }
        // create agency service
        return AgencyBusinessService::create($request);
    }
}
