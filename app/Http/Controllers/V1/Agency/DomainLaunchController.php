<?php

namespace App\Http\Controllers\V1\Agency;

use App\Http\Businesses\V1\Agency\PortalSettingBusiness;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Agency\DomainLaunchRequest;
use App\Http\Resources\V1\Agency\PortalSettingResponse;
use App\Http\Services\V1\Agency\AgencyDomainService;

/**
 * @group Agency Domain Launch
 * @authenticated
 */
class DomainLaunchController extends Controller
{
    public function index(DomainLaunchRequest $request)
    {
        $resp = AgencyDomainService::first(['domain' => $request->domain]);
        $setting  = PortalSettingBusiness::first($resp->agency_id);
        return new PortalSettingResponse($setting);
    }
}
