<?php

namespace App\Http\Businesses\V1\Customer;

use App\Exceptions\V1\AgencyException;
use App\Http\Services\V1\Customer\CustomerService;
use App\Http\Services\V1\Customer\UserService;
use App\Http\Services\V1\Customer\UserVerificationService;
use App\Http\Wrappers\SegmentWrapper;
use App\Models\Agency;

class CustomerBusiness
{
    public function register($request)
    {
        // add role
        $request->request->remove('role');
        $request->request->add(['role' =>  'Customer']);

        if (app('agency')->status == Agency::STATUS['pending']) {
            throw AgencyException::pending();
        }

        // create user
        $user = (new UserService())->create($request);


        // create customer
        $customer = (new CustomerService())->create($user);

        //assign Role
        (new UserService())->assignUserRole($request,$user);

        //segment registration event
        SegmentWrapper::registration($user);

        (new UserVerificationService())->generateVerificationCode($user);
    }
}
