<?php

namespace App\Http\Businesses\V1\Agency;


use App\Exceptions\V1\RequestValidationException;
use App\Http\Services\V1\Agency\PortalSettingService;
use Illuminate\Http\Request;

class PortalSettingBusiness
{
    public static function update(Request $request)
    {
        if ($request->has('logo') && !empty($request->logo) && !validate_base64($request->logo, ['png', 'jpg', 'jpeg'])) {
            throw RequestValidationException::errorMessage('Invalid image. Base64 image string is required. Allowed formats are png,jpg,jpeg.');
        }
        if ($request->has('favicon') && !empty($request->favicon) && !validate_base64($request->favicon, ['x-icon','png'])) {
            throw RequestValidationException::errorMessage('Invalid image. Base64 image string is required. Allowed formats are x-icon,png.');
        }
        PortalSettingService::update($request);
    }
}
