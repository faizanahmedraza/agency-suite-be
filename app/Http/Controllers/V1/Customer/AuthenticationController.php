<?php

namespace App\Http\Controllers\V1\Customer;

use App\Http\Resources\V1\Customer\UserResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\SuccessResponse;
use App\Http\Requests\V1\Customer\RegisterRequest;
use App\Http\Resources\V1\Customer\ProfileResponse;
use App\Http\Businesses\V1\Customer\CustomerBusiness;
use App\Http\Requests\V1\Customer\UpdateProfileRequest;
use App\Http\Businesses\V1\Customer\AuthenticationBusiness;

/**
 * @group Customer Register and Profile Settings
 */
class AuthenticationController extends Controller
{
    /**
     * Register Customer
     * This api is useful for register new Customer and return access token with Customer information
     *
     * @bodyParam first_name string required User first name Example: Bionic
     * @bodyParam last_name string required User last name Example: WP
     * @bodyParam email email required User email address Example: admin@bionicwp.com
     *
     * @header Domain string required
     * @header Client-ID string required
     * @header Client-Secret string required
     *
     * @responseFile 200 responses/SuccessResponse.json
     * @responseFile 422 responses/ValidationResponse.json
    */
    public function register(RegisterRequest $request)
    {
        DB::beginTransaction();
        (new CustomerBusiness())->register($request);
        DB::commit();
        return new SuccessResponse([]);
    }
}
