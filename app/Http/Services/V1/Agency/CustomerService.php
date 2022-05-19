<?php


namespace App\Http\Services\V1\Agency;

use App\Exceptions\V1\ModelException;
use App\Exceptions\V1\FailureException;
use App\Exceptions\V1\RequestValidationException;
use App\Http\Services\V1\Customer\CustomerService as AgencyCustomerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Models\User;

use Illuminate\Support\Facades\Auth;
use App\Helpers\TimeStampHelper;

class CustomerService
{
    public static function create($request)
    {
        $user = new User();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->username = clean($request->email);
        $user->password = Hash::make('12345678');
        $user->agency_id = app('agency')->id;
        $user->created_by = Auth::id();
        $user->save();

        $customer = (new AgencyCustomerService())->create($user);

        if (!$user || !$customer) {
            throw FailureException::serverError();
        }

        self::assignUserRole($request, $user);

        (new UserVerificationService())->generateVerificationCode($user);

        return $user->fresh();
    }

    public static function get(Request $request)
    {
        $users = User::query()->with('agencyCustomer');

        if ($request->query("customers")) {
            $ids = \getIds($request->customers);
            $users->orWhereIn('id', $ids);
        }

        if ($request->query('full_name')) {
            $fullName = User::clean($request->full_name);

            if (is_array($fullName)) {
                $users->whereRaw("CONCAT(TRIM(LOWER(first_name)) , ' ' ,TRIM(LOWER(last_name))) in ('" . join("', '", $fullName) . "')");
            } else {
                $users->whereRaw("CONCAT(LOWER(first_name) , ' ' ,LOWER(last_name)) like ? ", '%' . $fullName . '%');
            }
        }

        if ($request->query('first_name')) {
            $fname = User::clean($request->first_name);

            if (is_array($fname)) {
                $users->whereRaw("TRIM(LOWER(first_name)) in  ('" . join("', '", $fname) . "')");
            } else {
                $users->whereRaw('TRIM(LOWER(first_name)) like ?', '%' . $fname . '%');
            }
        }

        if ($request->query('last_name')) {
            $lname = User::clean($request->last_name);

            if (is_array($lname)) {
                $users->whereRaw("TRIM(LOWER(last_name)) in  ('" . join("', '", $lname) . "')");
            } else {
                $users->whereRaw('TRIM(LOWER(last_name)) like ?', '%' . $lname . '%');
            }
        }

        if ($request->query('email')) {
            $email = User::clean($request->email);

            if (is_array($email)) {
                $users->whereRaw("TRIM(LOWER(username)) in  ('" . join("', '", $email) . "')");
            } else {
                $users->whereRaw('TRIM(LOWER(username)) = ?', $email);
            }
        }

        if ($request->query('status')) {
            $users->where('status', User::STATUS[clean($request->status)]);
        }

        if ($request->query('order_by')) {
            $users->orderBy('id', $request->get('order_by'));
        } else {
            $users->orderBy('id', 'desc');
        }

        if ($request->query('from_date')) {
            $from = TimeStampHelper::formateDate($request->from_date);
            $users->whereDate('created_at', '>=', $from);
        }

        if ($request->query('to_date')) {
            $to = TimeStampHelper::formateDate($request->to_date);
            $users->whereDate('created_at', '<=', $to);
        }

        $users->whereHas('agencyCustomer', function ($q) {
            $q->where('agency_id', app('agency')->id);
        });

        return ($request->filled('pagination') && $request->get('pagination') == 'false')
            ? $users->get()
            : $users->paginate(\pageLimit($request));

    }

    public static function assignUserRole($request, $user)
    {
        if (!empty($request->get('role'))) {
            $user->assignRole($request->get('role'));
        }
    }

    public static function toggleStatus(User $user)
    {
        ($user->status == User::STATUS['pending'] || $user->status == User::STATUS['active']) ? $user->status = User::STATUS['blocked'] : $user->status = User::STATUS['active'];
        $user->save();

        if ($user->status = User::STATUS['blocked']) {
            AuthenticationService::revokeUserToken($user);
        }
    }

    public static function first(int $id, $with = []): User
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

    public static function update($request, User $user)
    {
        $user->first_name = trim($request->first_name);
        $user->last_name = trim($request->last_name);
        $user->updated_by = Auth::id();
        $user->save();

        if (!$user) {
            throw FailureException::serverError();
        }

        return $user;
    }

    public static function destroy(User $user)
    {
        if ($user->agencyCustomer->serviceRequests->isNotEmpty()) {
            throw RequestValidationException::errorMessage('Please delete the relational data first.', 422);
        }
        $user->delete();
    }
}
