<?php


namespace App\Http\Services\V1\Agency;

use App\Exceptions\V1\TokenException;
use App\Models\UserVerification;
use Illuminate\Support\Facades\DB;

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

    public function generateVerificationResponse($auth, $user, $agency)
    {
        return [
            'access_token' => $auth['token']->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => ($auth['token']->token->expires_at)->format('Y-m-d H:i:s'),
            'user' => $user,
            'agency' => $agency
        ];
    }

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

    public static function revokeUserToken($user)
    {
        return DB::table('oauth_access_tokens')->where('user_id', '=', $user->id)->update(['revoked' => true]);
    }
}
