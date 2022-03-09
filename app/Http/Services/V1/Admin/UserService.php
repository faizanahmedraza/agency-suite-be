<?php

namespace App\Http\Services\V1\Admin;

use App\Exceptions\V1\ModelException;
use App\Exceptions\V1\FailureException;
use App\Exceptions\V1\UnAuthorizedException;
use App\Exceptions\V1\UserException;

use App\Models\Admin;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Models\User;

use Illuminate\Support\Facades\Auth;
use App\Helpers\TimeStampHelper;
use Illuminate\Support\Str;

class UserService
{
    /**
     *  Create new User via required parameters. It will return User Object as Response
     *
     * @Param first_name
     * @Param last_name
     * @Param password
     * @Param email
     *
     *
     */
    public static function store(Request $request)
    {
        $password = empty($request->password) ? '12345678' : $request->password;

        $user = new User;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->password = Hash::make($password);
        $user->username = clean($request->email);
        $user->status = User::STATUS[$request->status] ? User::STATUS[$request->status] : User::STATUS['pending'];
        $user->created_by = Auth::id();
        $user->save();

        $user->admin()->save(new Admin());

        if (!$user) {
            throw FailureException::serverError();
        }

        return $user->load('permissions');
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

    public static function get(Request $request)
    {
        $users = User::query()->with(['roles', 'permissions']);

        if ($request->has("users")) {
            $ids = \getIds($request->users);
            $users->orWhereIn('id', $ids);
        }

        if ($request->has('full_name')) {
            $fullName = User::clean($request->full_name);

            if (is_array($fullName)) {
                $users->whereRaw("CONCAT(TRIM(LOWER(first_name)) , ' ' ,TRIM(LOWER(last_name))) in ('" . join("', '", $fullName) . "')");
            } else {
                $users->whereRaw("CONCAT(LOWER(first_name) , ' ' ,LOWER(last_name)) = ? ", $fullName);
            }
        }

        if ($request->has('first_name')) {
            $fname = User::clean($request->first_name);

            if (is_array($fname)) {
                $users->whereRaw("TRIM(LOWER(first_name)) in  ('" . join("', '", $fname) . "')");
            } else {
                $users->whereRaw('TRIM(LOWER(first_name)) = ?', $fname);
            }
        }

        if ($request->has('last_name')) {
            $lname = User::clean($request->last_name);

            if (is_array($lname)) {
                $users->whereRaw("TRIM(LOWER(last_name)) in  ('" . join("', '", $lname) . "')");
            } else {
                $users->whereRaw('TRIM(LOWER(last_name)) = ?', $lname);
            }
        }

        if ($request->has('email')) {
            $email = User::clean($request->email);

            if (is_array($email)) {
                $users->whereRaw("TRIM(LOWER(username)) in  ('" . join("', '", $email) . "')");
            } else {
                $users->whereRaw('TRIM(LOWER(username)) = ?', $email);
            }
        }

        if ($request->has('status')) {
            $arrStatus = getStatus(User::STATUS, clean($request->status));
            $users->wherein('status', $arrStatus);
        }

        if ($request->query('order_by')) {
            $users->orderBy('id', $request->get('order_by'));
        } else {
            $users->orderBy('id', 'desc');
        }

        if ($request->has('from_date')) {
            $from = TimeStampHelper::formateDate($request->from_date);
            $users->whereDate('created_at', '>=', $from);
        }

        if ($request->has('to_date')) {
            $to = TimeStampHelper::formateDate($request->to_date);
            $users->whereDate('created_at', '<=', $to);
        }

        $users->userRole();

        return ($request->filled('pagination') && $request->get('pagination') == 'false')
            ? $users->get()
            : $users->paginate(\pageLimit($request));

    }

    /**
     *  Get User Information by username
     *
     * @Param username
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

    public static function checkStatus(User $user)
    {
        if ($user->status == User::STATUS['blocked']) {
            throw UnAuthorizedException::accountBlocked();
        } else if ($user->status == User::STATUS['suspend']) {
            throw UserException::suspended();
        }
        return $user;
    }

    public static function blockUsers(array $ids)
    {
        return User::whereIn('id', $ids)->update(["status" => User::STATUS['blocked']]);
    }

    public static function first(int $id, $with = ['roles', 'permissions']): User
    {
        $user = User::with($with)
            ->where('id', $id)
            ->avoidRole(Role::userRoles()->pluck('name')->toArray())
            ->first();

        if (!$user) {
            throw ModelException::dataNotFound();
        }

        return $user;
    }

    public static function update($request, User $user)
    {
        $user->first_name = trim($request->first_name);
        $user->last_name = trim($request->last_name);
        $user->status = User::STATUS[$request->status] ? User::STATUS[$request->status] : User::STATUS['pending'];
        $user->updated_by = Auth::id();
        $user->save();

        $user->admin()->update([]);

        if (!$user) {
            throw FailureException::serverError();
        }

        return $user;
    }

    public static function destroy(User $user): void
    {
        $user->delete();
    }

    public static function toggleStatus(User $user): User
    {
        ($user->status == User::STATUS['pending'] || $user->status == User::STATUS['active']) ? $user->status = User::STATUS['blocked'] : $user->status = User::STATUS['active'];
        $user->save();

        if ($user->status = User::STATUS['blocked']) {
            AuthenticationService::revokeUserToken($user);
        }
    }

    public static function changePassword(User $user,$request)
    {
        self::checkStatus($user);

        $user->password = Hash::make($request->password);
        $user->save();

        if (!$user) {
            throw FailureException::serverError();
        }
    }

    /**
     *  Fetch User By Email
     *
     * @Param username
     */
    public static function getUserName($username, $excludeAuth = false)
    {
        if ($excludeAuth) {
            $user = User::whereRaw("LOWER(username) like ? ", '%' . $username . '%')->where('id', '!=', Auth::user()->id)->first();
        } else {
            $user = User::whereRaw("LOWER(username) like ? ", '%' . $username . '%')->first();
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
            return;
        }

        $token->expires_at = TimeStampHelper::getDate(10, $token->expires_at);
        $token->save();
    }
}
