<?php

namespace App\Http\Controllers\V1\Agency;

use App\Http\Businesses\V1\Agency\PortalSettingBusiness;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\Agency\PortalSettingResponse;
use Illuminate\Support\Facades\DB;

/**
 * @group Portal Settings
 */
class PublicPortalSettingController extends Controller
{
    /**
     * Public Portal Settings Api
     * This api update portal settings.
     *
     * @header Domain string required
     *
     * @responseFile 200 responses/V1/Agency/PortalSettingResponse.json
     */

    public function index()
    {
        DB::beginTransaction();
        $setting = PortalSettingBusiness::first();
        DB::commit();
        return new PortalSettingResponse($setting);
    }
}
