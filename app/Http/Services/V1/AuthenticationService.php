<?php


namespace App\Http\Services\V1;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;


/* Exceptions */
use App\Exceptions\V1\FailureException;
use App\Exceptions\V1\ModelException;
use App\Exceptions\V1\TokenException;


/* Models */
use App\Models\UserVerification;
use App\Models\User;

class AuthenticationService
{
    public function createToken($user)
    {
        $tokenResult = $user->createToken('Password Grant Client');
        $token = $tokenResult->token;
        $token->expires_at = (new \DateTime('now'))->modify("+10 day");
        $token->save();

        return $tokenResult;
    }

    public function generateVerificationResponse($auth, $user)
    {
        return [
            'access_token' => $auth['token']->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => ($auth['token']->token->expires_at)->format('Y-m-d H:i:s'),
            'user' => $user,

        ];
    }

    /**
     * Found Token
     * This function is useful to fetch the given token
     *
     * @param token
     *
     * @return UserVerification object
     *
     * @throws ModelException::dataNotFound()
     */
    public static function getUserVerification($token)
    {
        $userVerification = UserVerification::where('verification_code', $token)->first();
        if (!$userVerification) {
            throw TokenException::invalidToken();
        }

        return $userVerification;
    }

    public static function deleteToken(UserVerification $userVerification): void
    {
        $userVerification->delete();
    }

    public static function deleteUserToken(User $user): void
    {
        $tokens = UserVerification::where('user_id', $user->id)->get();
        if ($tokens) {
            $tokens->map(function ($token) {
                $token->delete();
            });
        }
    }
}
