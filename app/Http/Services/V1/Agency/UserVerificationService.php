<?php


namespace App\Http\Services\V1;

use Illuminate\Support\Str;

/* Exceptions */
use App\Exceptions\V1\ModelException;
use App\Exceptions\V1\FailureException;

/* Models */
use App\Models\UserVerification;
use App\Models\TokenException;

use App\Models\User;

class UserVerificationService
{

    /**
     *   generate unique code for verification and send it on email
     */
    public static function generateVerificationCode(User $user) : UserVerification
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

    /**
     * Get User Info By Token
     * fethch user information through token
     *
     * @param  required $token string(35)
     *
     * @return User Object
     */

    public function getUserInfoFromToken($token)
    {
        $token = UserVerification::where('verification_code', $token)->first();
        if (!$token) {
            throw TokenException::invalidToken();
        }
        return $token;
    }
}
