<?php

namespace App\Http\Businesses\V1\Agency;

use App\Events\LoginEvent;
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
