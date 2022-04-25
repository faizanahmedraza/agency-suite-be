<?php


namespace App\Http\Services\V1\Agency;

use App\Models\User;
use App\Models\Agency;
use App\Helpers\TimeStampHelper;
use App\Exceptions\V1\UserException;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Exceptions\V1\ModelException;

use App\Exceptions\V1\FailureException;
use App\Http\Services\V1\CloudinaryService;

class AgencyService
{
    public static function create($data)
    {
        $agency = new Agency();
        $agency->name = trim($data->agency_name);
        $agency->status = Agency::STATUS['pending'];
        $agency->save();

        if (!$agency) {
            throw FailureException::serverError();
        }

        return $agency;
    }

    public static function updateStatus(Agency $agency, $status = Agency::STATUS['active'])
    {
        $agency->status = $status;
        $agency->save();

        return $agency;
    }

    public static function first($with = [], $where = null)
    {
        $agency = Agency::with($with)->where($where)->first();

        if (!$agency) {
            throw ModelException::dataNotFound();
        }

        return $agency;
    }

    public static function updateProfile($request,User $user=null)
    {
        if($user==null){
            $user=Auth::user();
        }
        $user->first_name = trim($request->name);
        $user->last_name = trim($request->name);
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
