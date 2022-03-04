<?php

namespace App\Http\Controllers\V1\Agency;

use App\Http\Businesses\V1\AuthenticationBusiness;
use App\Http\Businesses\V1\UserBusiness;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\CreateNewPasswordRequest;
use App\Http\Requests\V1\ForgetPasswordRequest;
use App\Http\Requests\V1\LoginRequest;
use App\Http\Requests\V1\RegisterRequest;
use App\Http\Requests\V1\UserVerificationRequest;
use App\Http\Resources\SuccessResponse;
use App\Http\Resources\V1\AuthenticationResponse;
use App\Http\Resources\V1\UserResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthenticationController extends Controller
{
    /**
     * Access Token Or Login
     * This function is useful for login, to return access token for users.
     *
     * @bodyParam email email required The username of user. Example: admin@my-app.com
     * @bodyParam password string required The password of user. Example: Abc*123*
     *
     * @headerParam Client-ID string required
     * @headerParam Client-Secret string required
     *
     * @responseFile 200 app/Http/Resources/V1/AuthenticationResponse.php
     * @responseFile 422 app/Http/Requests/V1/LoginRequest.php
     */

    public function login(LoginRequest $request)
    {
        $auth = (new AuthenticationBusiness)->verifyLoginInfo($request);
        return new AuthenticationResponse($auth);
    }

    /**
     * Register Customer
     * This api is useful for generate new Customer and return access token with user information
     *
     * @bodyParam first_name string required User first name Example: Bionic
     * @bodyParam last_name string required User last name Example: WP
     * @bodyParam email email required User email address Example: admin@bionicwp.com
     * @bodyParam password string required User password Example: abcd1234
     * @bodyParam password_confirmation string required User password Example: abcd1234
     *
     * @headerParam Client-ID string required
     * @headerParam Client-Secret string required
     *
     * @responseFile 200 app/Http/Resources/V1/AuthenticationResponse.php
     * @responseFile 422 app/Http/Requests/V1/RegisterRequest.php
     */
    public function register(RegisterRequest $request)
    {
        DB::beginTransaction();
        $user = (new UserBusiness)->register($request);
        DB::commit();
        return new AuthenticationResponse($user);
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
     * @responseFile 200 app/Http/Resources/SuccessResponse.php
     * @responseFile 422 app/Http/Requests/V1/UserVerificationRequest.php
     */
    public function userVerification(UserVerificationRequest $request)
    {
        DB::beginTransaction();
        AuthenticationBusiness::tokenValidation($request);
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
     * @responseFile 200 app/Http/Resources/SuccessResponse.php
     * @responseFile 422 app/Http/Requests/V1/ForgetPasswordRequest.php
     */
    public function forgetPassword(ForgetPasswordRequest $request)
    {
        DB::beginTransaction();
        AuthenticationBusiness::forgetPassword($request);
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
     * @responseFile 200 app/Http/Resources/SuccessResponse.php
     * @responseFile 422 app/Http/Requests/V1/CreateNewPasswordRequest.php
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
     * @responseFile 200 app/Http/Resources/SuccessResponse.php
     * @responseFile 422 vendor/illuminate/http/Request.php
     */
    public static function logout(Request $request)
    {
        AuthenticationBusiness::logout($request);
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
     * @responseFile 200 app/Http/Resources/SuccessResponse.php
     */
    public function generateToken()
    {
        AuthenticationBusiness::generateToken();
        return new SuccessResponse([]);
    }
}
