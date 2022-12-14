<?php

namespace App\Http\Businesses\V1\Agency;


use App\Exceptions\V1\DomainException;
use App\Exceptions\V1\RequestValidationException;
use App\Http\Services\V1\Agency\AgencyDomainService;
use App\Http\Services\V1\Agency\PortalSettingService;
use App\Models\AgencyDomain;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PortalSettingBusiness
{
    public static function first($agency_id = null)
    {
        return PortalSettingService::first($agency_id);
    }

    public static function update(Request $request)
    {
        if ($request->has('logo') && !empty($request->logo) && !validate_base64($request->logo, ['png', 'jpg', 'jpeg']) && !Str::contains($request->logo, ['https', 'cloudinary'])) {
            throw RequestValidationException::errorMessage('Invalid logo. Base64 logo string is required. Allowed formats are png,jpg,jpeg.');
        }
        if ($request->has('favicon') && !empty($request->favicon) && !validate_base64($request->favicon, ['x-icon', 'png']) && !Str::contains($request->favicon, ['https', 'cloudinary'])) {
            throw RequestValidationException::errorMessage('Invalid favicon. Base64 favicon string is required. Allowed formats are x-icon,png.');
        }

        if (isset($request->domain) && !empty($request->domain)) {
            $newDomain = AgencyDomain::cleanDomain($request->domain);
            $domainFilter = AgencyDomain::domainsFilter($newDomain)->first();
            if (!empty($domainFilter) && $domainFilter->agency_id != app('agency')->id) {
                throw DomainException::alreadyAvaliable();
            }

            $domainData = new \stdClass;
            $domainData->agency_id = app('agency')->id;
            $domainData->domain = $newDomain;
            $domainData->type = AgencyDomain::TYPE['custom'];
            $domainData->default = true;

            $customDomain = AgencyDomainService::customDomain();

            if (empty($customDomain)) {
                AgencyDomainService::markDefault();
                AgencyDomainService::create($domainData, false);
            } elseif ($customDomain->domain != $newDomain) {
                AgencyDomainService::markDefault();
                AgencyDomainService::update($customDomain, $domainData);
            }
        } else {
            $customDomain = AgencyDomainService::customDomain();
            if (!empty($customDomain)) {
                AgencyDomainService::deleteDomain($customDomain);
                AgencyDomainService::markDefault(true);
            }
        }

        return PortalSettingService::update($request);
    }
}
