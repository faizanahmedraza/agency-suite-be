<?php

namespace App\Http\Businesses\V1;

use App\Http\Services\V1\AuthenticationService;
use App\Http\Services\V1\UserService;
use App\Http\Services\V1\UserVerificationService;
use Illuminate\Support\Facades\Auth;

class UserBusiness
{
    public static function first()
    {
        return UserService::first(Auth::user());
    }

    public static function update($request)
    {
        $user = UserService::first(Auth::user());
        // update in users table
        UserService::update($request, $user);

        return $user;
    }

    public static function changePassword($request)
    {
        UserService::changeUserPassword(Auth::user(), $request->password);
    }

    public function register($request)
    {
        // create user
        $request->request->add(['role' =>  'Business']);
        $user = (new UserService())->create($request);

        //auth services
        $authService = new AuthenticationService();
        $auth['token'] = $authService->createToken($user);

        (new UserVerificationService())->generateVerificationCode($user);

        return $authService->generateVerificationResponse($auth, $user);
    }
}
