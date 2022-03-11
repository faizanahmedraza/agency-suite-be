<?php

namespace App\Http\Businesses\V1\Agency;

use App\Http\Services\V1\Agency\AgencyService;
use App\Http\Services\V1\Agency\AuthenticationService;
use App\Http\Services\V1\Agency\UserService;
use App\Http\Services\V1\Agency\UserVerificationService;

class AgencyBusiness
{
    public function register($request)
    {
        // add role
        $request->request->remove('role');
        $request->request->add(['role' =>  'Agency']);

        // create agency
        $agency = (new AgencyService())->create($request);

        // create agency owner
        $user = (new UserService())->create($request,$agency, true);

        //auth services
        $authService = new AuthenticationService();
        $auth['token'] = $authService->createToken($user);

        (new UserVerificationService())->generateVerificationCode($user);

        return $authService->generateVerificationResponse($auth, $user, $agency);
    }
}
