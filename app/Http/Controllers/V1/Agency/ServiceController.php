<?php

namespace App\Http\Controllers\V1\Agency;

use App\Http\Businesses\V1\Agency\ServiceBusiness;
use App\Http\Businesses\V1\Agency\UserBusiness;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Agency\ChangeAnyPasswordRequest;
use App\Http\Requests\V1\Agency\ServiceRequest;
use App\Http\Requests\V1\Agency\UserListRequest;
use App\Http\Requests\V1\Agency\UserRequest;
use App\Http\Resources\SuccessResponse;
use App\Http\Resources\V1\Agency\ServiceResponse;
use App\Http\Resources\V1\Agency\UsersResponse;
use App\Http\Resources\V1\Agency\UserResponse;
use Illuminate\Support\Facades\DB;

/**
 * @group Agency Services
 * @authenticated
 */
class ServiceController extends Controller
{
//    private $module;
//
//    public function __construct()
//    {
//        $this->module = 'agency_services';
//        $ULP = '|' . $this->module . '_all|agency_access_all'; //UPPER LEVEL PERMISSIONS
//        $this->middleware('permission:' . $this->module . '_read' . $ULP, ['only' => ['first', 'get', 'search']]);
//        $this->middleware('permission:' . $this->module . '_create' . $ULP, ['only' => ['store']]);
//        $this->middleware('permission:' . $this->module . '_update' . $ULP, ['only' => ['update']]);
//        $this->middleware('permission:' . $this->module . '_delete' . $ULP, ['only' => ['destroy']]);
//        $this->middleware('permission:' . $this->module . '_toggle_status' . $ULP, ['only' => ['toggleStatus']]);
//    }

    /**
     * Create Services
     * This api create new service.
     *
     * @bodyParam  name string required
     * @bodyParam  description string required
     * @bodyParam  image string ex: base64imageFile
     * @bodyParam  subscription_type string ex: 'one-off' ,'recurring'
     * @bodyParam  price integer required if subscription_type is one_off ex: 123
     * @bodyParam  purchase_limit integer optional if subscription_type is one_off ex: 12
     * @bodyParam  weekly integer required if subscription_type is recurring ex: 123
     * @bodyParam  monthly integer required if subscription_type is recurring ex: 123
     * @bodyParam  quarterly integer required if subscription_type is recurring ex: 123
     * @bodyParam  biannually integer required if subscription_type is recurring ex: 123
     * @bodyParam  annually integer required if subscription_type is recurring ex: 123
     * @bodyParam  max_concurrent_requests integer optional if subscription_type is recurring ex: 12
     * @bodyParam  max_requests_per_month integer optional if subscription_type is recurring ex: 12
     *
     * @responseFile 200 responses/V1/Agency
     * @responseFile 422 responses/ValidationResponse.json
     * @responseFile 401 responses/UnAuthorizedResponse.json
     */

    public function store(ServiceRequest $request)
    {
        DB::beginTransaction();
        $service = ServiceBusiness::store($request);
        DB::commit();
        return (new ServiceResponse($service));
    }

    /**
     * Get Services
     * This will return logged in user profile.
     *
     * @urlParam services string 1,2,3,4
     * @urlParam email string ex: abc.com,xyz.co
     * @urlParam first_name string
     * @urlParam last_name string
     * @urlParam full_name string
     * @urlParam status string ex: pending,active,blocked
     * @urlParam order_by string ex: asc/desc
     * @urlParam from_date string Example: Y-m-d
     * @urlParam to_date string Example: Y-m-d
     * @urlParam pagination boolean
     * @urlParam page_limit integer
     * @urlParam page integer
     * @urlParam roles string ex: admin,test
     *
     * @responseFile 200 responses/V1/Admin/UsersResponse.json
     * @responseFile 422 responses/ValidationResponse.json
     * @responseFile 401 responses/UnAuthorizedResponse.json
     */

    public function get(UserListRequest $request)
    {
        $services = UserBusiness::get($request);
        return (new UsersResponse($services));
    }

    /**
     * Show User Details
     * This api show the uer details.
     *
     * @urlParam  user_id required Integer
     *
     * @responseFile 200 responses/V1/Admin/UserResponse.json
     * @responseFile 422 responses/ValidationResponse.json
     * @responseFile 401 responses/UnAuthorizedResponse.json
     */
    public function first(int $id)
    {
        $user = UserBusiness::first($id);
        return (new UserResponse($user));
    }


    /**
     * Update Services
     * This api update service.
     *
     * @bodyParam  name string required
     * @bodyParam  description string required
     * @bodyParam  image string ex: base64imageFile
     * @bodyParam  subscription_type string ex: 'one-off' ,'recurring'
     * @bodyParam  price integer required if subscription_type is one_off ex: 123
     * @bodyParam  purchase_limit integer optional if subscription_type is one_off ex: 12
     * @bodyParam  weekly integer required if subscription_type is recurring ex: 123
     * @bodyParam  monthly integer required if subscription_type is recurring ex: 123
     * @bodyParam  quarterly integer required if subscription_type is recurring ex: 123
     * @bodyParam  biannually integer required if subscription_type is recurring ex: 123
     * @bodyParam  annually integer required if subscription_type is recurring ex: 123
     * @bodyParam  max_concurrent_requests integer optional if subscription_type is recurring ex: 12
     * @bodyParam  max_requests_per_month integer optional if subscription_type is recurring ex: 12
     *
     * @responseFile 200 responses/V1/Agency
     * @responseFile 422 responses/ValidationResponse.json
     * @responseFile 401 responses/UnAuthorizedResponse.json
     */

    public function update(UserRequest $request, int $id)
    {
        DB::beginTransaction();
        $user = UserBusiness::update($request, $id);
        DB::commit();
        return (new UserResponse($user));
    }

    /**
     * Delete User
     *
     * This api delete user
     *
     * @urlParam  user_id required Integer
     *
     * @responseFile 200 responses/SuccessResponse.json
     * @responseFile 401 responses/UnAuthorizedResponse.json
     */

    public function destroy(int $id)
    {
        UserBusiness::destroy($id);
        return new SuccessResponse([]);
    }

    /**
     * Toggle User Status
     * This api update the services status to active or deactive
     * other then customers.
     *
     * @urlParam user_id integer required
     *
     * @responseFile 200 responses/SuccessResponse.json
     * @responseFile 401 responses/UnAuthorizedResponse.json
     */

    public static function toggleStatus(int $id)
    {
        UserBusiness::toggleStatus($id);
        return new SuccessResponse([]);
    }

    /**
     * Change Password of Any User
     *
     * @urlParam id integer required
     *
     * @bodyParam password string required ex: 123456
     *
     * @responseFile 200 responses/SuccessResponse.json
     */

    public static function changeAnyPassword(ChangeAnyPasswordRequest $request, int $id)
    {
        UserBusiness::changeAnyPassword($request, $id);
        return new SuccessResponse([]);
    }
}
