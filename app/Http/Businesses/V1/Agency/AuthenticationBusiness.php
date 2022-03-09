<?php

namespace App\Http\Businesses\V1\Agency;

use App\Events\LoginEvent;
use App\Exceptions\V1\RequestValidationException;
use App\Exceptions\V1\UserException;
use App\Helpers\TimeStampHelper;
use App\Http\Services\V1\Agency\AuthenticationService;
use App\Http\Services\V1\Agency\UserService;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\V1\UnAuthorizedException;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthenticationBusiness
{
    public function verifyLoginInfo($request)
    {
        // get user data from database
        $user = (new UserService())->getUserByUsername($request->email);

        if (empty($user->agency_id)) {
            throw UnAuthorizedException::InvalidCredentials();
        }

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

    public static function tokenValidation($request): void
    {
        $authService = new AuthenticationService();
        $userService = new UserService();

        // verify user token
        $userVerification = $authService->getUserVerification($request->token);

        // get user data by user id
        $user = $userService->getUserById($userVerification->user_id);

        if ($user->status !== User::STATUS['pending']) {
            throw UserException::userAlreadyActive();
        }

        // validate token expiry
        $tokenExpiry = TimeStampHelper::expiryValidation(new \DateTime($userVerification->expiry));
        if (!$tokenExpiry) {
            throw RequestValidationException::errorMessage("Token has been expired");
        }

        // Delete Token
        $authService->deleteToken($userVerification);
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

    public function changePassword($request)
    {
        UserService::changePassword(Auth::user(), $request);
    }
}
