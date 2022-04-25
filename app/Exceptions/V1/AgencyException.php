<?php

namespace App\Exceptions\V1;

use App\Exceptions\BaseException;

class AgencyException extends BaseException
{
    public static function pending(): self
    {
        return new self(
            'Agency is not verified yet, if you would like quicker access please contact support',
            '401'
        );
    }

    public static function blocked(): self
    {
        return new self(
            'Agency is blocked, if you would like quicker access please contact support',
            '403'
        );
    }

    public static function customMsg($msg,$code=422): self
    {
        return new self(
            $msg,
            $code
        );
    }
}
