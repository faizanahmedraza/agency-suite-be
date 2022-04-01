<?php

namespace App\Http\Controllers\V1\Agency;

use App\Http\Businesses\V1\Agency\PortalSettingBusiness;
use App\Http\Businesses\V1\Agency\ServiceBusiness;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Agency\PortalSettingRequest;
use App\Http\Requests\V1\Agency\ServiceRequest;
use App\Http\Resources\SuccessResponse;
use App\Http\Resources\V1\Agency\ServiceResponse;
use Illuminate\Support\Facades\DB;

/**
 * @group Portal Settings
 * @authenticated
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
     * @header Domain string required
     *
     * @bodyParam  name string
     * @bodyParam  logo string ex: base64imageFile formats: png,jpeg,jpg
     * @bodyParam  favicon string ex: base64imageFile formats: png,x-icon
     * @bodyParam  primary_color string ex: '#fsfsd'
     *
     * @responseFile 200 responses/SuccessResponse.json
     * @responseFile 401 responses/UnAuthorizedResponse.json
     * @responseFile 422 responses/ValidationResponse.json
     */

    public function update(PortalSettingRequest $request)
    {
        DB::beginTransaction();
        PortalSettingBusiness::update($request);
        DB::commit();
        return new SuccessResponse([]);
    }
}
