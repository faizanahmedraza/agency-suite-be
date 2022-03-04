<?php

namespace App\Http\Businesses\V1\Admin;

use App\Http\Services\V1\Admin\AuthenticationService;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\V1\UnAuthorizedException;

use App\Http\Services\V1\Admin\UserService;

use Illuminate\Support\Facades\Hash;

use App\Models\User;

class AuthenticationBusiness
{
    public function verifyLoginInfo($request)
    {
        // get user data from database
        $user = (new UserService())->getUserByUsername($request->email);

        // match password
        if (!Hash::check($request->password, $user->password)) {
            throw UnAuthorizedException::InvalidCredentials();
        }

        //auth services
        $authService = new AuthenticationService();
        $auth['token'] = $authService->createToken($user);
        return $authService->generateVerificationResponse($auth, $user);
    }

    public static function logout($request)
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
        $user = (new UserService())->first();
        (new UserService())->changePassword($user, $request->password);
    }
}
