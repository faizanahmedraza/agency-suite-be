<?php

namespace App\Exceptions\V1;

use App\Exceptions\BaseException;

class DomainException extends BaseException
{
    public static function alreadyAvaliable(): self
    {
        return new self(
            'Domain already exist',
            '422'
        );
    }

    public static function notAllowed(): self
    {
        return new self(
            'The domain you are adding is already your staging domain.',
            '422'
        );
    }

    public static function agencyDomainNotExist(): self
    {
        return new self(
            'Request is from unknown source.',
            '404'
        );
    }

    public static function hostRequired(): self
    {
        return new self(
            'Domain is required in headers.',
            '422'
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
