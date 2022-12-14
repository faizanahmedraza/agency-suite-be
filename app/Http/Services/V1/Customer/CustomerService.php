<?php


namespace App\Http\Services\V1\Customer;

use App\Exceptions\V1\ModelException;
use App\Exceptions\V1\FailureException;
use App\Exceptions\V1\UnAuthorizedException;
use App\Exceptions\V1\UserException;

use App\Models\Customer;
use Illuminate\Support\Facades\Hash;

use App\Models\User;

use Illuminate\Support\Facades\Auth;
use App\Helpers\TimeStampHelper;

class CustomerService
{
    public static function create($data)
    {
        $customer = new Customer();
        $customer->user_id = $data->id;
        $customer->agency_id = $data->agency_id;
        $customer->save();

        if (!$customer) {
            throw FailureException::serverError();
        }

        return $customer;
    }

    /**
     *  This function is used to assign Role to User
     */
    public static function assignUserRole($request, $user)
    {
        if (!empty($request->get('role'))) {
            $user->assignRole($request->get('role'));
        }
    }

    /**
     * Get User Info
     * Get user information from username = email
     *
     * @param username = $email Ex: admin@mail.com
     *
     * @return User Object
     *
     */
    public static function getUserByEmail(String $email): User
    {
        $user = User::where('username', $email)->first();
        if (!$user) {
            throw ModelException::dataNotFound();
        }
        return $user;
    }

    /** Update User Password
     *
     * @param  required User $id , Request $password
     *
     * @throw FailureException
     *
     * @return $user object
     */
    public static function changeUserPassword(User $user, String $password): User
    {
        $user->password = Hash::make($password);
        $user->save();

        if (!$user) {
            throw FailureException::serverError();
        }

        return $user;
    }

    /**
     *  Get User Information by username
     *
     *  @Param username
     */
    public static function getUserByUsername($username)
    {
        $user = User::whereRaw('LOWER(username) = ? ', strtolower($username))->first();

        if (!$user) {
            throw UnAuthorizedException::InvalidCredentials();
        }
        return $user;
    }

    public static function getUserById(Int $id): User
    {
        $user = User::where(['id' => $id])->first();

        if (!$user) {
            throw UnAuthorizedException::InvalidCredentials();
        }
        return $user;
    }

    public static function updateStatus(User $user, $status = User::STATUS['active']): User
    {
        $user->status = $status;
        $user->save();

        return $user;
    }

    public static function blockUsers(array $ids)
    {
        return User::whereIn('id', $ids)->update(["status" => User::STATUS['blocked']]);
    }

    public static function first($with = [], $where = null): User
    {
        $user = User::with($with)->where($where)->first();

        if (!$user) {
            throw ModelException::dataNotFound();
        }

        return $user;
    }

    public static function update($request, User $user)
    {
        $user->first_name = trim($request->first_name);
        $user->last_name = trim($request->last_name);
        $user->save();

        if (!$user) {
            throw FailureException::serverError();
        }

        return $user;
    }

    /**
     *  Fetch User By Email
     *
     *  @Param username
     */
    public static function getUserName($username, $excludeAuth = false)
    {
        if ($excludeAuth) {
            $user = User::whereRaw("LOWER(username) like ? ", '%' . $username . '%')->where('id', '!=', Auth::user()->id)->first();
        } else {
            $user =  User::whereRaw("LOWER(username) like ? ", '%' . $username . '%')->first();
        }

        if ($user) {
            return strtolower($user->username);
        }
        return false;
    }


    public static function validateToken($token)
    {
        if ($token->expires_at < TimeStampHelper::now()) {
            throw UserException::sessionExpired();
        }

        $days = TimeStampHelper::countAccurateDays($token->expires_at, TimeStampHelper::now());

        if ($days > 1) {
            return ;
        }

        $token->expires_at =  TimeStampHelper::getDate(10, $token->expires_at);
        $token->save();
    }
}
