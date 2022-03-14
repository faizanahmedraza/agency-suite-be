<?php

namespace App\Http\Controllers\V1\Agency;

use App\Http\Businesses\V1\Agency\PermissionBusiness;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\Agency\PermissionsResponse;

/**
 * @group Agency Permissions Api
 * @authenticated
 */
class PermissionController extends Controller
{
    private $module;

    public function __construct()
    {
        $this->module = 'agency_permissions';
        $ULP = '|' .'agency_access_all'; //UPPER LEVEL PERMISSIONS
        $this->middleware('permission:' . $this->module . '_list'. $ULP, ['only' => ['get']]);
    }

    /**
     * Permission List
     * This api return the collection of all Permissions created.
     *
     * @header Authorization String required Example: Bearer TOKEN
     *
     * @responseFile 200 responses/V1/Admin/PermissionsResponse.json
     */

    public function get()
    {
        $permissions = PermissionBusiness::get();
        return (new PermissionsResponse($permissions));
    }
}
