<?php

namespace App\Http\Controllers\V1\Customer;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\SuccessResponse;
use App\Http\Requests\V1\Customer\LoginRequest;
use App\Http\Requests\V1\Customer\RegisterRequest;
use App\Http\Resources\V1\Customer\ProfileResponse;
use App\Http\Businesses\V1\Customer\CustomerBusiness;
use App\Http\Requests\V1\Customer\ForgetPasswordRequest;
use App\Http\Requests\V1\Customer\UserVerificationRequest;
use App\Http\Resources\V1\Customer\AuthenticationResponse;
use App\Http\Businesses\V1\Customer\AuthenticationBusiness;
use App\Http\Requests\V1\Customer\CreateNewPasswordRequest;

/**
 * @group Customer Authentication
 */
class AuthenticationController extends Controller
{
    /**
     * Access Token Or Login
     * This function is useful for login, to return access token for customer.
     *
     * @bodyParam email email required The username of user. Example: customer@my-app.com
     * @bodyParam password string required The password of user. Example: Abc*123*
     *
     * @header Domain string required
     * @header Client-ID string required
     * @header Client-Secret string required
     *
     * @responseFile 200 responses/V1/Customer/AuthenticationResponse.json
     * @responseFile 422 responses/ValidationResponse.json
     */

    public function login(LoginRequest $request)
    {
        $auth = (new AuthenticationBusiness())->verifyLoginInfo($request);
        return new AuthenticationResponse($auth);
    }

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

    /**
     * Verify Customer
     * This function is useful to verify a new customer
     *
     * @header Domain string required
     * @header Client-ID string required
     * @header Client-Secret string required
     *
     * @queryParam token String required T5oqVFXCYiDjUtZpzvXJXvzw2xJClHNA Example: T5oqVFXCYiDjUtZpzvXJXvzw2xJClHNA
     * @bodyParam password String required abcd1234 Example: abcd1234
     * @bodyParam password_confirmation String required  abcd1234 Example: abcd1234
     *
     * @responseFile 200 responses/V1/Customer/AuthenticationResponse.json
     * @responseFile 422 responses/ValidationResponse.json
     */
    public function userVerification(UserVerificationRequest $request)
    {
        DB::beginTransaction();
        $auth = (new AuthenticationBusiness())->userVerification($request);
        DB::commit();
        return new AuthenticationResponse($auth);
    }

    /**
     * Forgot Password
     * This api will send an email to valid customer with token for resetting the password
     *
     * @bodyParam email String required User valid email address Example: user@bionicwp.com
     *
     * @header Domain string required
     * @header Client-ID string required
     * @header Client-Secret string required
     *
     * @responseFile 200 responses/SuccessResponse.json
     * @responseFile 422 responses/ValidationResponse.json
     */
    public function forgetPassword(ForgetPasswordRequest $request)
    {
        DB::beginTransaction();
        (new AuthenticationBusiness())->forgetPassword($request);
        DB::commit();
        return new SuccessResponse([]);
    }

    /**
     * Create New Password
     * authenticate customer request and then create new password
     *
     * @queryParam token String required T5oqVFXCYiDjUtZpzvXJXvzw2xJClHNA Example: T5oqVFXCYiDjUtZpzvXJXvzw2xJClHNA
     * @bodyParam password String required abcd1234 Example: abcd1234
     * @bodyParam password_confirmation String required  abcd1234 Example: abcd1234
     *
     * @header Domain string required
     * @header Client-ID string required
     * @header Client-Secret string required
     *
     * @responseFile 200 responses/SuccessResponse.json
     * @responseFile 422 responses/ValidationResponse.json
     */
    public function createNewPassword(CreateNewPasswordRequest $request)
    {
        DB::beginTransaction();
        (new AuthenticationBusiness())->validateAndCreateNewPassword($request);
        DB::commit();
        return new SuccessResponse([]);
    }

    /**
     * Logout
     * Hit api and session logout
     *
     * @authenticated
     *
     * @header Domain string required
     * @header Authorization string required
     *
     * @responseFile 200 responses/SuccessResponse.json
     * @responseFile 422 responses/ValidationResponse.json
     */
    public static function logout(Request $request)
    {
        (new AuthenticationBusiness())->logout($request);
        return new SuccessResponse([]);
    }

     /**
     * Profile
     * Get Profile Info
     *
     * @authenticated
     *
     * @header Domain string required
     *
     * @responseFile 200 responses/V1/Customer/ProfileResponse.json
     * @responseFile 422 responses/ValidationResponse.json
     */
    public static function profile()
    {
        return new ProfileResponse(Auth::user());
    }
}
