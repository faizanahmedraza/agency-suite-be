<?php

namespace App\Http\Controllers\V1\Agency;

use App\Http\Resources\V1\Admin\UserResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\SuccessResponse;
use App\Http\Requests\V1\Agency\LoginRequest;
use App\Http\Requests\V1\Agency\RegisterRequest;
use App\Http\Businesses\V1\Agency\AgencyBusiness;
use App\Http\Resources\V1\Agency\ProfileResponse;
use App\Http\Requests\V1\Agency\UpdateProfileRequest;
use App\Http\Requests\V1\Agency\ChangePasswordRequest;
use App\Http\Requests\V1\Agency\ForgetPasswordRequest;
use App\Http\Requests\V1\Agency\UserVerificationRequest;
use App\Http\Resources\V1\Agency\AuthenticationResponse;
use App\Http\Resources\V1\Agency\RedirectDomainResponse;
use App\Http\Businesses\V1\Agency\AuthenticationBusiness;
use App\Http\Requests\V1\Agency\CreateNewPasswordRequest;

/**
 * @group Authentication and Agency Profile Settings
 */
class AuthenticationController extends Controller
{
    private $module;

    public function __construct()
    {
        $this->module = 'agency_profile';
        $ULP = '|' . $this->module . '_all|agency_access_all'; //UPPER LEVEL PERMISSIONS
        $this->middleware('permission:' . $this->module . '_read' . $ULP, ['only' => ['profile']]);
        $this->middleware('permission:' . $this->module . '_update' . $ULP, ['only' => ['profileUpdate']]);
    }

    /**
     * Access Token Or Login
     * This function is useful for login, to return access token for agencies.
     *
     * @bodyParam email email required The username of user. Example: admin@my-app.com
     * @bodyParam password string required The password of user. Example: Abc*123*
     *
     * @header Domain string required
     * @header Client-ID string required
     * @header Client-Secret string required
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
     * @bodyParam agency_name string required Example: abc-agency, abc agency
     * @bodyParam email email required User email address Example: admin@bionicwp.com
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
        (new AgencyBusiness())->register($request);
        DB::commit();
        return new SuccessResponse([]);
    }

    /**
     * Verify Agency
     * This function is used to verify a new agency
     *
     * @header Domain string required
     * @header Client-ID string required
     * @header Client-Secret string required
     *
     * @queryParam token string required S0OoOuegYqgQX8JMnbovfnaV7QjMEHLc Example: S0OoOuegYqgQX8JMnbovfnaV7QjMEHLc
     * @bodyParam password String required abcd1234 Example: abcd1234
     * @bodyParam password_confirmation String required  abcd1234 Example: abcd1234
     *
     * @responseFile 200 responses/V1/Agency/AuthenticationResponse.json
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
     * This api will send an email to valid user with token for resetting his/her password
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
     * authenticate user request and then create new password
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
     * Hit api and session get out
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
     * Generate Verification Token
     * This function generate new verification token of user
     *
     * @authenticated
     *
     * @header Domain string required
     * @header Authorization string required
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
     * @header Domain string required
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

    /**
     * Profile
     * Get Profile Info
     *
     * @authenticated
     *
     * @header Domain string required
     *
     * @responseFile 200 responses/V1/Agency/ProfileResponse.json
     * @responseFile 422 responses/ValidationResponse.json
     */
    public static function profile()
    {
        return new ProfileResponse(Auth::user());
    }

    /**
     * Update Profile
     *
     * @authenticated
     *
     * @header Domain string required
     *
     * @bodyParam name String required
     * @bodyParam image String optional ex: base64imageFile formats: png,jpeg,jpg
     *
     * @responseFile 200 responses/SuccessResponse.json
     * @responseFile 422 responses/ValidationResponse.json
     */
    public function profileUpdate(UpdateProfileRequest $request)
    {
        DB::beginTransaction();
        $user = (new AuthenticationBusiness())->profileUpdate($request);
        DB::commit();
        return new UserResponse($user);
    }


}
