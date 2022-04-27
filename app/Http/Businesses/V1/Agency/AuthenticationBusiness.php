<?php

namespace App\Http\Businesses\V1\Agency;

use App\Exceptions\V1\AgencyException;
use App\Models\User;
use App\Models\Agency;
use App\Events\LoginEvent;
use App\Helpers\TimeStampHelper;
use App\Exceptions\V1\UserException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Wrappers\SegmentWrapper;
use App\Exceptions\V1\UnAuthorizedException;
use App\Http\Services\V1\Agency\UserService;
use App\Http\Services\V1\Agency\AgencyService;
use App\Exceptions\V1\RequestValidationException;
use App\Http\Services\V1\Agency\AuthenticationService;
use App\Http\Services\V1\Agency\UserVerificationService;
use Illuminate\Support\Str;

class AuthenticationBusiness
{
    public function verifyLoginInfo($request)
    {
        // get user data from database
        $user = (new UserService())->getUserByAgency([
            ['username', '=', clean($request->email)],
            ['agency_id', '=', (app('agency'))->id],
        ]);

        if (app('agency')->status == Agency::STATUS['pending']) {
            throw AgencyException::pending();
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

        //check user status
        if ($user->status == User::STATUS['blocked']) {
            throw UnAuthorizedException::accountBlocked();
        } elseif ($user->status == User::STATUS['suspend']) {
            throw UserException::suspended();
        }

        UserService::updateStatus($user);

        if ($user->owner) {
            $agency = AgencyService::first($userVerification->agency_id,['domains']);
            AgencyService::updateStatus($agency);
        }

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
            ['username', '=', clean($request->email)],
            ['agency_id', '=', (app('agency'))->id],
        ]);
        //check user status
        if ($user->status == User::STATUS['blocked']) {
            throw UnAuthorizedException::accountBlocked();
        } elseif ($user->status == User::STATUS['suspend']) {
            throw UserException::suspended();
        }
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

    public function changePassword($request)
    {
        $user = Auth::user();
        // check user status
        UserService::checkStatus($user);
        //segment create password event
        SegmentWrapper::createPassword($user);
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
        //segment verification token event
        SegmentWrapper::generateToken($user);
        UserVerificationService::generateVerificationCode($user);
    }

    public function profileUpdate($request)
    {
        if ($request->has('image') && !empty($request->image) && !Str::contains($request->image, ['res', 'https', 'cloudinary']) && !validate_base64($request->image, ['png', 'jpg', 'jpeg'])) {
            throw RequestValidationException::errorMessage('Invalid image. Base64 image string is required. Allowed formats are png,jpg,jpeg.');
        }
        // update profile
        return AgencyService::updateProfile($request);
    }
}
