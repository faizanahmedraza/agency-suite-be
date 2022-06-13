<?php

namespace App\Http\Controllers\V1\Agency;

use App\Http\Businesses\V1\Agency\PortalSettingBusiness;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Agency\PortalSettingRequest;
use App\Http\Resources\V1\Agency\PortalSettingResponse;
use Illuminate\Support\Facades\DB;

/**
 * @group Portal Settings
 */
class PortalSettingController extends Controller
{
    private $module;

    public function __construct()
    {
        $this->module = 'agency_portal_settings';
        $ULP = '|' . $this->module . '_all|agency_access_all'; //UPPER LEVEL PERMISSIONS
        $this->middleware('permission:' . $this->module . '_update' . $ULP, ['only' => ['update']]);
    }

    /**
     * Update Portal Settings
     * This api update portal settings.
     *
     * @authenticated
     * @header Domain string required
     *
     * @bodyParam  name string required ex: Agency Name
     * @bodyParam  domain string required ex: myagency.test
     * @bodyParam  logo string ex: base64imageFile formats: png,jpeg,jpg
     * @bodyParam  favicon string ex: base64imageFile formats: png,x-icon
     * @bodyParam  primary_color string ex: '#fsfsd'
     * @bodyParam  secondary_color string ex: '#fsfsd'
     *
     * @responseFile 200 responses/V1/Agency/PortalSettingResponse.json
     * @responseFile 401 responses/UnAuthorizedResponse.json
     * @responseFile 422 responses/ValidationResponse.json
     */

    public function update(PortalSettingRequest $request)
    {
        DB::beginTransaction();
        $setting = PortalSettingBusiness::update($request);
        DB::commit();
        return new PortalSettingResponse($setting);
    }
}
