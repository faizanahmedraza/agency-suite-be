<?php


namespace App\Http\Services\V1\Agency;

use App\Models\User;
use App\Models\Agency;

use Illuminate\Support\Facades\Auth;

use App\Exceptions\V1\ModelException;

use App\Exceptions\V1\FailureException;
use App\Http\Services\V1\CloudinaryService;
use Illuminate\Support\Str;

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

    public static function first($id, $with = [])
    {
        $agency = Agency::with($with)->where('id', $id)->first();

        if (!$agency) {
            throw ModelException::dataNotFound();
        }

        return $agency;
    }

    public static function updateProfile($request, User $user = null)
    {
        if ($user == null) {
            $user = Auth::user();
        }
        $user->first_name = trim($request->first_name);
        $user->last_name = trim($request->last_name);
        if ($request->has('image') && !empty($request->image) && !Str::contains($request->image, ['res', 'https', 'cloudinary'])) {
            $user->image = CloudinaryService::upload($request->image)->secureUrl;
        }
        $user->save();

        if (!$user) {
            throw FailureException::serverError();
        }

        return $user;
    }
}
