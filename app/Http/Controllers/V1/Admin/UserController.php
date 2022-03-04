<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;

use App\Http\Resources\SuccessResponse;

use Illuminate\Support\Facades\DB;

/**
 * @group Admin
 * @group User Management
 */
class UserController extends Controller
{
    private $module;

    public function __construct()
    {
        $this->module = 'users';
        $ULP = '|' . $this->module . '_all|access_all'; //UPPER LEVEL PERMISSIONS
        $this->middleware('permission:' . $this->module . '_read' . $ULP, ['only' => ['first', 'get', 'search']]);
        $this->middleware('permission:' . $this->module . '_create' . $ULP, ['only' => ['store']]);
        $this->middleware('permission:' . $this->module . '_update' . $ULP, ['only' => ['update']]);
        $this->middleware('permission:' . $this->module . '_delete' . $ULP, ['only' => ['destroy']]);
        $this->middleware('permission:' . $this->module . '_toggle_status' . $ULP, ['only' => ['toggleStatus']]);
    }

    /**
     * Create Users
     * This api create new user.
     *
     * @authenticated
     *
     * @bodyParam  first_name String required
     * @bodyParam  last_name String required
     * @bodyParam  email email required
     * @bodyParam  password String required
     * @bodyParam  password_confirmation String required
     * @bodyParam  phone email Optional
     * @bodyParam  roles Array required
     * @bodyParam  permissions Array
     *
     * @responseFile 200 responses/SuccessResponse.json
     * @responseFile 200 responses/V1/User/DetailsResponse.json
     * @responseFile 401 responses/UnAuthorizedResponse.json
     */

    public function store(UserRequest $request)
    {
        DB::beginTransaction();
        $user = UserBusiness::store($request);
        DB::commit();
        return (new UserResponse($user));
    }

    /**
     * Get Users
     * This will return logged in user profile.
     *
     * @authenticated
     *
     * @urlParam users string [1,2,3,4]
     * @urlParam status string ['pending' => 0, 'active' => 1, 'blocked' => 2]
     * @urlParam to_register_date timestamp Optional
     * @urlParam from_register_date timestamp Optional
     *
     * @responseFile 200 responses/V1/User/ListResponse.json
     * @responseFile 401 responses/UnAuthorizedResponse.json
     *
     */

    public function get(Request $request)
    {
        $users = UserBusiness::get($request);
        return (new UsersResponse($users));
    }

    /**
     * Show User Details
     * This api show the uer details.
     *
     * @urlParam  user_id required Integer
     *
     *
     * @responseFile 200 responses/V1/User/DetailsResponse.json
     * @responseFile 401 responses/UnAuthorizedResponse.json
     */
    public function first(int $id)
    {
        $user = UserBusiness::first($id);
        return (new UserResponse($user));
    }


    /**
     * Update User Details.
     * This api update user details
     *
     * @bodyParam  first_name String required
     * @bodyParam  last_name String required
     * @bodyParam  email email required
     * @bodyParam  roles Array required
     * @bodyParam  permissions Array
     *
     * @urlParam  user_id Integer required
     *
     * @responseFile 200 responses/V1/User/DetailsResponse.json
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
     * This api update the users status to active or deactive
     * other then customers.
     *
     * @authentiacte
     *
     * @urlParam user_id required
     *
     * @responseFile 200 responses/SuccessResponse.json
     */

    public static function toggleStatus(int $id)
    {
        $user = UserBusiness::toggleStatus($id);
        return new SuccessResponse($user);
    }

    /**
     *
     * Smart Search For Users
     * This requeired overall two parameter one for fields and other for values
     * Fields attr can only have below defined parameter
     * While value field can only have required string or email to search.
     * Example:
     * field = email, full_name
     * value = bionic@integrit.pk, bionic
     *
     * @urlParam field required
     * @urlParam value required
     *
     * @responseFile 200 responses/V1/SmartSearchResponse.json
     *
     */
    public static function search(Request $request)
    {
        $users = UserBusiness::search($request);
        return (new SmartSearchResponse($users));
    }


    /**
     * Toggle User Status
     * This api update user password
     * other then customers.
     *
     * @authentiacte
     *
     * @urlParam id required
     * @bodyParam password required string
     *
     * @responseFile 200 responses/SuccessResponse.json
     */

    public static function passwordChange(PasswordChangeRequest $request, int $id)
    {
        $user = UserBusiness::passwordChange($request, $id);
        return new SuccessResponse($user);
    }
}
