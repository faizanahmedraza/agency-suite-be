<?php

namespace App\Exceptions\V1;

use App\Exceptions\BaseException;
use App\Exceptions\Error;

class UserException extends BaseException
{
    public static function userAlreadyActive(): self
    {
        return new self(
            'User you are trying to activate is already activated. If you are facing any issue please contact customer support team.',
            '422'
        );
    }

    public static function permission(): self
    {
        return new self(
            'User doesnt has Permission',
            '403'
        );
    }


    public static function suspended(): self
    {
        return new self(
            'Your account is suspended',
            '403'
        );
    }

    public static function sessionExpired(): self
    {
        return new self(
            'User session has expired',
            '401'
        );
    }

    public static function emailAlreadyExist(): self
    {
        return new self(
            'Email already Exists.',
            '403'
        );
    }

    public static function Unauthorized(): self
    {
        return new self(
            'Unauthorized access! User does not have access.',
            '401'
        );
    }

    public static function invalidUserCredentials(): self
    {
        return new self(
            'Invalid credentials! Email or Password is invalid.',
            '403'
        );
    }
}
