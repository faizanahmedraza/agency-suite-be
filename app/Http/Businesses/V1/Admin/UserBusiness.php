<?php

namespace App\Http\Businesses\V1\Admin;

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
}
