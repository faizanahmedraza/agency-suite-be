<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Businesses\V1\Admin\RoleBusiness;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Admin\RoleRequest;
use App\Http\Resources\SuccessResponse;
use App\Http\Resources\V1\Admin\RoleResponse;
use App\Http\Resources\V1\Admin\RolesResponse;
use Illuminate\Support\Facades\DB;

/**
 * @group Admin
 * @group Roles Management
 * @authenticated
 */
class RoleController extends Controller
{
    private $module;

    public function __construct()
    {
        $this->module = 'roles';
        $ULP = '|' . $this->module . '_all|access_all'; //UPPER LEVEL PERMISSIONS
        $this->middleware('permission:' . $this->module . '_read' . $ULP, ['only' => ['first', 'get']]);
        $this->middleware('permission:' . $this->module . '_create' . $ULP, ['only' => ['store']]);
        $this->middleware('permission:' . $this->module . '_update' . $ULP, ['only' => ['update']]);
        $this->middleware('permission:' . $this->module . '_delete' . $ULP, ['only' => ['destroy']]);
    }

    /**
     * Role List
     * This api return the collection of all Roles created.
     *
     * @headerParam Authorization String required Example: Bearer TOKEN
     *
     * @responseFile 200 responses/V1/Admin/RolesResponse.json
     */

    public function get()
    {
        $roles = RoleBusiness::get();
        return (new RolesResponse($roles));
    }

    /**
     * Create Role
     * This api is for create new Role
     *
     * @headerParam Authorization String required Example: Bearer TOKEN
     *
     * @bodyParam name String required
     * @bodyParam permissions array optional
     *
     *
     * @responseFile 200 responses/V1/Admin/RoleResponse.json
     * @responseFile 422 responses/ValidationResponse.json
     */

    public function store(RoleRequest $request)
    {
        DB::beginTransaction();
        $role = RoleBusiness::store($request);
        DB::commit();
        return (new RoleResponse($role));
    }

    /**
     * Role Details
     * This api show the details of requested Role.
     *
     * @headerParam Authorization String required Example: Bearer TOKEN
     *
     * @urlParam role_id required Integer
     *
     * @responseFile 200 responses/V1/Admin/RoleResponse.json
     * @responseFile 422 responses/ValidationResponse.json
     *
     */

    public function first(int $id)
    {
        $role = RoleBusiness::first($id);
        return (new RoleResponse($role));
    }

    /**
     * Role Update
     * This api update the details of requested Role.
     *
     * @headerParam Authorization String required Example: Bearer TOKEN
     *
     * @urlParam role_id required Integer
     *
     * @bodyParam name String required
     * @bodyParam permissions array optional
     *
     * @responseFile 200 responses/V1/Admin/RoleResponse.json
     * @responseFile 422 responses/ValidationResponse.json
     *
     */

    public function update(RoleRequest $request, $id)
    {
        DB::beginTransaction();
        $role = RoleBusiness::update($request, $id);
        DB::commit();
        return (new RoleResponse($role));
    }

    /**
     * Delete Role
     *
     * Delete Requested Record
     *
     * @headerParam Authorization String required Example: Bearer TOKEN
     *
     * @urlParam role_id required Integer
     *
     * @responseFile 200 responses/SuccessResponse.json
     * @responseFile 422 responses/ValidationResponse.json
     *
     * */

    public function destroy(int $id)
    {
        RoleBusiness::destroy($id);
        return new SuccessResponse([]);
    }
}
