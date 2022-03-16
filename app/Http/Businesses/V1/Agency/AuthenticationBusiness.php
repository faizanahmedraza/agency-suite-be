<?php

namespace App\Http\Businesses\V1\Agency;

use App\Models\User;
use App\Models\Agency;
use App\Events\LoginEvent;
use App\Helpers\TimeStampHelper;
use App\Exceptions\V1\UserException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Exceptions\V1\TokenException;
use App\Exceptions\V1\DomainException;
use App\Exceptions\V1\UnAuthorizedException;
use App\Http\Services\V1\Agency\UserService;
use App\Exceptions\V1\RequestValidationException;
use App\Http\Services\V1\Agency\AuthenticationService;
use App\Http\Services\V1\Agency\UserVerificationService;

class AuthenticationBusiness
{
    public function verifyLoginInfo($request)
    {
        // get user data from database
        $user = (new UserService())->getUserByAgency([
            ['username', '=', $request->email],
            ['agency_id', '=', (app('agency'))->id],
        ]);

        // match password
        if (!Hash::check($request->password, $user->password)) {
            throw UnAuthorizedException::InvalidCredentials();
        }

        // check user status
        UserService::checkStatus($user);

        //auth services
        $authService = new AuthenticationService();
        $auth['token'] = $authService->createToken($user);

        //last login tracking event
        event(new LoginEvent($user));
        return $authService->generateVerificationResponse($auth, $user, $user->agency);
    }

    public function tokenValidation($request)
    {

        $authService = new AuthenticationService();

        // verify user token
        $userVerification = $authService->getUserVerification($request->token);

        // validate token expiry
        $tokenExpiry = TimeStampHelper::expiryValidation(new \DateTime($userVerification->expiry));
        if (!$tokenExpiry) {
            throw RequestValidationException::errorMessage("Token has been expired. Please contact our support team.");
        }

        UserService::updateStatus($userVerification->user);

        $agency = Agency::where('id', $userVerification->agency_id)->first();

        // Delete Token
        $authService->deleteToken($userVerification);

        return $agency->domains->first();
    }

    public function forgetPassword($request): void
    {
        $user = (new UserService())->getUserByAgency([
            ['username', '=', $request->email],
            ['agency_id', '=', (app('agency'))->id],
        ]);
        UserVerificationService::generateVerificationCode($user);
    }

    public function validateAndCreateNewPassword($request)
    {
        $authService = new AuthenticationService();

        $userVerification = $authService->getUserVerification($request->token);
        $tokenExpiry = TimeStampHelper::expiryValidation(new \DateTime($userVerification->expiry));
        if (!$tokenExpiry) {
            throw RequestValidationException::errorMessage("Token has been expired");
        }

        (new UserService())->changePassword($userVerification->user, $request->password);

        $authService->deleteToken($userVerification);
    }

    public function changePassword($request)
    {
        $user = Auth::user();
        UserService::changePassword($user, $request->password);
    }

    public function logout($request)
    {
        if (Auth::check()) {
            if (!$request->filled('action')) {
                $user = Auth::user();
                $user->token()->revoke();
            } else {
                User::revokeToken(Auth::user());
            }
        }
    }

    public static function generateToken()
    {
        $user = Auth::user();

        if ($user->status == User::STATUS['active']) {
            throw UserException::userAlreadyActive();
        }

        AuthenticationService::deleteUserToken($user);
        UserVerificationService::generateVerificationCode($user);
    }
}
