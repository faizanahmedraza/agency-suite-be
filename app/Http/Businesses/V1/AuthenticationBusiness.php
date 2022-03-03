<?php

namespace App\Http\Businesses\V1;

/* Exceptions */

use App\Helpers\TimeStampHelper;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\V1\UnAuthorizedException;
use App\Exceptions\V1\RequestValidationException;
use App\Exceptions\V1\UserException;

/* Services */
use App\Http\Services\V1\UserVerificationService;
use App\Http\Services\V1\AuthenticationService;
use App\Http\Services\V1\UserService;

/* Helpers */
use Illuminate\Support\Facades\Hash;

/* Models */
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

    /**
     * Validat Token
     * This function is useful for validate if token
     * exis and expiration date is valid
     *
     * @param token
     *
     * @return void
     *
     * @throws RequestValidationException::errorMessage()
     */

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

    public function validateAndCreateNewPassword($request)
    {
        $authService = new AuthenticationService();
        $userService = new UserService();

        $userVerification = $authService->getUserVerification($request->token);
        $tokenExpiry = TimeStampHelper::expiryValidation(new \DateTime($userVerification->expiry));
        if (!$tokenExpiry) {
            throw RequestValidationException::errorMessage("Token has been expired");
        }


        $user = $userService->getUserById($userVerification->user_id);
        (new UserService())->changeUserPassword($user, $request->password);

        $authService->deleteToken($userVerification);
    }

    public static function forgetPassword($request): void
    {
        $user = UserService::getUserByEmail(strtolower($request->email));
        UserVerificationService::generateVerificationCode($user);
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

    
    public static function generateToken()
    {
        $user = Auth::user();

        if ($user->status == User::STATUS['active']) {
            throw UserException::userAlreadyActive();
        }

        AuthenticationService::deleteUserToken($user);
        UserVerificationService::generateVerificationCode($user);
    }


    public static function authenticate()
    {
        return UserService::first(Auth::user());
    }
}
