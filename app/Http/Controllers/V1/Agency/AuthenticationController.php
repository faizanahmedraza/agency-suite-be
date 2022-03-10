<?php

namespace App\Http\Controllers\V1\Agency;

use App\Http\Businesses\V1\Agency\AgencyBusiness;
use App\Http\Businesses\V1\Agency\AuthenticationBusiness;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Agency\ChangePasswordRequest;
use App\Http\Requests\V1\Agency\CreateNewPasswordRequest;
use App\Http\Requests\V1\Agency\ForgetPasswordRequest;
use App\Http\Requests\V1\Agency\LoginRequest;
use App\Http\Requests\V1\Agency\RegisterRequest;
use App\Http\Requests\V1\Agency\UserVerificationRequest;
use App\Http\Resources\SuccessResponse;
use App\Http\Resources\V1\Agency\AuthenticationResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * @group Agency Authentication
 */
class AuthenticationController extends Controller
{
    /**
     * Access Token Or Login
     * This function is useful for login, to return access token for agencies.
     *
     * @bodyParam email email required The username of user. Example: admin@my-app.com
     * @bodyParam password string required The password of user. Example: Abc*123*
     *
     * @headerParam Client-ID string required
     * @headerParam Client-Secret string required
     *
     * @responseFile 200 responses/V1/Agency/AuthenticationResponse.json
     * @responseFile 422 responses/ValidationResponse.json
     */

    public function login(LoginRequest $request)
    {
        $auth = (new AuthenticationBusiness())->verifyLoginInfo($request);
        return new AuthenticationResponse($auth);
    }

    /**
     * Register Agency
     * This api is useful for register new Agency and return access token with agency and user information
     *
     * @bodyParam first_name string required User first name Example: Bionic
     * @bodyParam last_name string required User last name Example: WP
     * @bodyParam email email required User email address Example: admin@bionicwp.com
     * @bodyParam password string required User password Example: abcd1234
     * @bodyParam password_confirmation string required User password Example: abcd1234
     * @bodyParam agency_name string required Example: abc-agency, abc agency
     *
     * @headerParam Client-ID string required
     * @headerParam Client-Secret string required
     *
     * @responseFile 200 responses/V1/Agency/AuthenticationResponse.json
     * @responseFile 422 responses/ValidationResponse.json
     */
    public function register(RegisterRequest $request)
    {
        DB::beginTransaction();
        $agency = (new AgencyBusiness())->register($request);
        DB::commit();
        return new AuthenticationResponse($agency);
    }

    /**
     * Verify Token
     * This function is useful to check the token is valid or not
     *
     * @bodyParam token string required S0OoOuegYqgQX8JMnbovfnaV7QjMEHLc Example: S0OoOuegYqgQX8JMnbovfnaV7QjMEHLc
     *
     * @headerParam Client-ID string required
     * @headerParam Client-Secret string required
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
     * This api will send an email to valid user with token for resetting his/her password
     *
     * @bodyParam email String required User valid email address Example: user@bionicwp.com
     *
     * @headerParam Client-ID string required
     * @headerParam Client-Secret string required
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
     * authenticate user request and then create new password
     *
     * @bodyParam token String required T5oqVFXCYiDjUtZpzvXJXvzw2xJClHNA Example: T5oqVFXCYiDjUtZpzvXJXvzw2xJClHNA
     * @bodyParam password String required abcd1234 Example: abcd1234
     * @bodyParam password_confirmation String required  abcd1234 Example: abcd1234
     *
     * @headerParam Client-ID string required
     * @headerParam Client-Secret string required
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
     * Hit api and session get out
     *
     * @authenticated
     *
     * @headerParam Authorization string required
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
     * Generate Verification Token
     * This function generate new verification token of user
     *
     * @authenticated
     *
     * @headerParam Authorization string required
     *
     * @responseFile 200 responses/SuccessResponse.json
     */
    public function generateToken()
    {
        AuthenticationBusiness::generateToken();
        return new SuccessResponse([]);
    }

    /**
     * Change Password
     * change password request of user
     *
     * @authenticated
     *
     * @bodyParam password String required abcd1234 Example: abcd1234
     * @bodyParam password_confirmation String required  abcd1234 Example: abcd1234
     *
     * @responseFile 200 responses/SuccessResponse.json
     * @responseFile 422 responses/ValidationResponse.json
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        DB::beginTransaction();
        (new AuthenticationBusiness())->changePassword($request);
        DB::commit();
        return new SuccessResponse([]);
    }
}
