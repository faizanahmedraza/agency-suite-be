<?php

namespace App\Http\Controllers\V1\Customer;

use App\Http\Businesses\V1\Customer\CustomerBusiness;
use App\Http\Businesses\V1\Customer\AuthenticationBusiness;
use App\Http\Controllers\Controller;
// use App\Http\Requests\V1\Agency\ChangePasswordRequest;
use App\Http\Requests\V1\Customer\CreateNewPasswordRequest;
use App\Http\Requests\V1\Customer\ForgetPasswordRequest;
use App\Http\Requests\V1\Customer\LoginRequest;
use App\Http\Requests\V1\Customer\RegisterRequest;
use App\Http\Requests\V1\Customer\UserVerificationRequest;
use App\Http\Resources\SuccessResponse;
use App\Http\Resources\V1\Agency\AuthenticationResponse;
// use App\Http\Resources\V1\Agency\RedirectDomainResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
     * @bodyParam password string required User password Example: abcd1234
     * @bodyParam password_confirmation string required User password Example: abcd1234
     *
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
     * Verify Token
     * This function is useful to check the token is valid or not
     *
     * @queryParam token string required S0OoOuegYqgQX8JMnbovfnaV7QjMEHLc Example: S0OoOuegYqgQX8JMnbovfnaV7QjMEHLc
     *
     * @header Client-ID string required
     * @header Client-Secret string required
     *
     * @responseFile 200 responses/SuccessResponse.json
     * @responseFile 422 responses/ValidationResponse.json
     */
    public function userVerification(UserVerificationRequest $request)
    {

        DB::beginTransaction();
        (new AuthenticationBusiness())->tokenValidation($request);
        DB::commit();
        return new SuccessResponse([]);
    }

    /**
     * Forgot Password
     * This api will send an email to valid customer with token for resetting the password
     *
     * @bodyParam email String required User valid email address Example: user@bionicwp.com
     *
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

    // /**
    //  * Generate Verification Token
    //  * This function generate new verification token of user
    //  *
    //  * @authenticated
    //  *
    //  * @header Authorization string required
    //  *
    //  * @responseFile 200 responses/SuccessResponse.json
    //  */
    // public function generateToken()
    // {
    //     AuthenticationBusiness::generateToken();
    //     return new SuccessResponse([]);
    // }

    // /**
    //  * Change Password
    //  * change password request of user
    //  *
    //  * @authenticated
    //  *
    //  * @bodyParam password String required abcd1234 Example: abcd1234
    //  * @bodyParam password_confirmation String required  abcd1234 Example: abcd1234
    //  *
    //  * @responseFile 200 responses/SuccessResponse.json
    //  * @responseFile 422 responses/ValidationResponse.json
    //  */
    // public function changePassword(ChangePasswordRequest $request)
    // {
    //     DB::beginTransaction();
    //     (new AuthenticationBusiness())->changePassword($request);
    //     DB::commit();
    //     return new SuccessResponse([]);
    // }
}
