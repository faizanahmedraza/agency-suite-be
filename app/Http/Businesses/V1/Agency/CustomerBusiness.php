<?php

namespace App\Http\Businesses\V1\Agency;

use App\Exceptions\V1\UserException;
use App\Http\Services\V1\Agency\CustomerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerBusiness
{
    public static function store($request)
    {
        // add role
        $request->request->remove('role');
        $request->request->add(['role' => 'Customer']);

        return CustomerService::create($request);
    }

    public static function get($request)
    {
        return CustomerService::get($request);
    }

    public static function first(int $id)
    {
        return CustomerService::first($id);
    }

    public static function update($request, int $id)
    {
        $customer = CustomerService::first($id);
        return CustomerService::update($request,$customer);
    }

    public static function destroy(int $id): void
    {
        $customer = CustomerService::first($id);
        CustomerService::destroy($customer);
    }

    public static function toggleStatus($id,Request $request)
    {
        // get user
        $user = CustomerService::first($id);

        if ($user->id == Auth::id()) {
            throw UserException::authUserRestrictStatus();
        }
        // status toggle
        CustomerService::toggleStatus($user,$request);
    }
}
