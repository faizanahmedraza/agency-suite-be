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
            'Add domain is not allowed on staging sites.',
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
}
