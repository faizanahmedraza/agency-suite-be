<?php

namespace App\Exceptions\V1;

use App\Exceptions\BaseException;

class UnAuthorizedException extends BaseException
{
    public static function UserUnAuthorized(): self
    {
        return new self("User does not have valid access token!", '403');
    }

    public static function InvalidCredentials(): self
    {
        return new self("Invalid Email or Password", '401');
    }

    public static function accountBlocked(): self
    {
        return new self("Your account has been blocked. Please contact with administrator.s", '401');
    }

    public static function unVerifiedAccount(): self
    {
        return new self("Verify your account by clicking on the link sent to your email address.", '401');
    }

    public static function review(): self
    {
        return new self("Your account is in review. Please contact with support for more details", '401');
    }
}
