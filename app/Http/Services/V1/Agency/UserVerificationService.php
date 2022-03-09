<?php


namespace App\Http\Services\V1\Agency;

use App\Exceptions\V1\TokenException;
use Illuminate\Support\Str;

use App\Exceptions\V1\FailureException;

use App\Models\UserVerification;

use App\Models\User;

class UserVerificationService
{
    public static function generateVerificationCode($user)
    {
        $userVerification = new UserVerification();
        $userVerification->user_id = $user->id;
        $userVerification->verification_code =  Str::random(32);
        $userVerification->expiry = (new \DateTime("now"))->modify('+2 days');
        $userVerification->save();

        if (!$userVerification) {
            throw FailureException::serverError();
        }

        return $userVerification;
    }

    public function getUserInfoFromToken($token)
    {
        $token = UserVerification::where('verification_code', $token)->first();
        if (!$token) {
            throw TokenException::invalidToken();
        }
        return $token;
    }
}
