<?php

namespace App\Http\Businesses\V1\Customer;

use App\Http\Services\V1\Customer\CustomerService;
use App\Http\Services\V1\Customer\UserService;
use App\Http\Services\V1\Customer\UserVerificationService;
use App\Http\Wrappers\SegmentWrapper;

class CustomerBusiness
{
    public function register($request)
    {
        // add role
        $request->request->remove('role');
        $request->request->add(['role' =>  'Customer']);

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
