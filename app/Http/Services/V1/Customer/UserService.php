<?php


namespace App\Http\Services\V1\Customer;

use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\TimeStampHelper;
use App\Exceptions\V1\UserException;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Exceptions\V1\ModelException;

use App\Exceptions\V1\FailureException;
use App\Http\Services\V1\CloudinaryService;
use App\Exceptions\V1\UnAuthorizedException;

class UserService
{
    public static function create($request)
    {
        $user = new User();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->password = Hash::make('12345678');
        $user->username = clean($request->email);
        $user->status = User::STATUS['pending'];
        $user->agency_id = app('agency')->id;
        $user->owner = null;
        $user->save();

        if (!$user) {
            throw FailureException::serverError();
        }

        return $user->fresh();
    }

    public static function assignUserRole($request, $user)
    {
        if (!empty($request->get('role'))) {
            $user->assignRole($request->get('role'));
        }
    }

    public static function updateStatus(User $user, $status = User::STATUS['active'])
    {
        $user->status = $status;
        $user->save();
    }

    public static function getUserByAgency($where = null)
    {
        $user = User::where($where)->first();

        if (!$user) {
            throw UnAuthorizedException::InvalidCredentials();
        }

        return $user;
    }

    public static function checkStatus(User $user)
    {
        if ($user->status == User::STATUS['blocked']) {
            throw UnAuthorizedException::accountBlocked();
        } else if ($user->status == User::STATUS['suspend']) {
            throw UserException::suspended();
        } else if ($user->status == User::STATUS['pending']) {
            throw UnAuthorizedException::pendingAccount();
        }
        return $user;
    }

    public static function changePassword(User $user, String $password)
    {
        self::checkStatus($user);

        $user->password = Hash::make($password);
        $user->save();

        if (!$user) {
            throw FailureException::serverError();
        }
    }

    public static function first(int $id, $with = ['roles', 'agency']): User
    {
        $user = User::with($with)
            ->where('id', $id)
            ->where('agency_id', app('agency')->id)
            ->avoidRole(['Super Admin', 'Agency'])
            ->first();

        if (!$user) {
            throw ModelException::dataNotFound();
        }

        return $user;
    }

    public static function updateCustomerProfile($request,User $user=null)
    {
        if($user==null){
            $user=Auth::user();
        }
        $user->first_name = trim($request->first_name);
        $user->last_name = trim($request->last_name);
        if ($request->has('image') && !empty($request->image)) {
            $user->image = CloudinaryService::upload($request->image)->secureUrl;
        }
        $user->save();

        if (!$user) {
            throw FailureException::serverError();
        }

        return $user;
    }
}
