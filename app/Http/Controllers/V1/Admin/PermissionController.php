<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
/* Responses*/
use App\Http\Resources\V1\PermissionResponse;
use App\Http\Resources\V1\PermissionsResponse;
use App\Http\Resources\SuccessResponse;
/* Request */
use App\Http\Requests\V1\PermissionRequest;
/* Business */
use App\Http\Businesses\V1\PermissionBusiness;

/**
 * @group Permissions Api
 */

class PermissionController extends Controller
{
    private $module;

    public function __construct()
    {
        $this->module = 'permissions';
        $ULP = '|' . $this->module . '_all|access_all'; //UPPER LEVEL PERMISSIONS
        $this->middleware('permission:' . $this->module . '_list'. $ULP, ['only' => ['get']]);
    }

    /**
     * Permission List
     * This api return the collection of all Permissions created.
     *
     * @headerParam Authorization String required Example: Bearer TOKEN
     *
     * @responseFile 200 responses/V1/Permission/ListResponse.json
     */

    public function get()
    {
        $permissions = PermissionBusiness::get();
        return (new PermissionsResponse($permissions));
    }
}
