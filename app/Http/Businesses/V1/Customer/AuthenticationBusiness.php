<?php

namespace App\Http\Businesses\V1\Customer;

use App\Http\Wrappers\SegmentWrapper;
use App\Models\User;
use App\Events\LoginEvent;
use App\Helpers\TimeStampHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Exceptions\V1\UnAuthorizedException;
use App\Http\Services\V1\Customer\UserService;
use App\Exceptions\V1\RequestValidationException;
use App\Http\Services\V1\Customer\AuthenticationService;
use App\Http\Services\V1\Customer\UserVerificationService;

class AuthenticationBusiness
{
    public function verifyLoginInfo($request)
    {
        // get user data from database
        $user = (new UserService())->getUserByAgency([
            ['username', '=', clean($request->email)],
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

        //segment login event
        SegmentWrapper::login($user);
        return $authService->generateVerificationResponse($auth, $user, $user->agency);
    }

    public function userVerification($request)
    {
        $authService = new AuthenticationService();

        // verify user token
        $userVerification = $authService->getUserVerification($request->token);

        // validate token expiry
        $tokenExpiry = TimeStampHelper::expiryValidation(new \DateTime($userVerification->expiry));
        if (!$tokenExpiry) {
            throw RequestValidationException::errorMessage("Token has been expired. Please contact our support team.");
        }

        $user = (new UserService())->first($userVerification->user->id);

        UserService::updateStatus($user);

        (new UserService())->changePassword($user, $request->password);

        // Delete Token
        $authService->deleteToken($userVerification);

        //auth access token
        $auth['token'] = $authService->createToken($user);

        //segment user verification event
        SegmentWrapper::userVerification($user);

        return $authService->generateVerificationResponse($auth, $user, $user->agency);
    }

    public function forgetPassword($request): void
    {
        $user = (new UserService())->getUserByAgency([
            ['username', '=', $request->email],
            ['agency_id', '=', (app('agency'))->id],
        ]);
        //segment forgot password event
        SegmentWrapper::forgotPassword($user);
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

        //segment create password event
        SegmentWrapper::createPassword($userVerification->user);

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
}
