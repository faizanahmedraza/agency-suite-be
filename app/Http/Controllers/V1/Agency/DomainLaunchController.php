<?php

namespace App\Http\Controllers\V1\Agency;

use App\Http\Businesses\V1\Agency\PortalSettingBusiness;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Agency\DomainLaunchRequest;
use App\Http\Resources\V1\Agency\PortalSettingResponse;
use App\Http\Services\V1\Agency\AgencyDomainService;

/**
 * @group Agency Domain Launch
 */
class DomainLaunchController extends Controller
{
    /**
     * Public Agency Domain Launch Api
     * This api to verify the domain before jump to agency portal.
     *
     * @bodyParam domain string required
     *
     * @responseFile 422 responses/ValidationResponse.json
     * @responseFile 200 responses/V1/Agency/PortalSettingResponse.json
     */
    public function index(DomainLaunchRequest $request)
    {
        $resp = AgencyDomainService::first(['domain' => $request->domain]);
        $setting  = PortalSettingBusiness::first($resp->agency_id);
        return new PortalSettingResponse($setting);
    }
}
