<?php

namespace App\Exceptions\V1;

use App\Exceptions\BaseException;

class PaymentException extends BaseException
{
    public static function gatewayNotFound(): self
    {
        return new self(
            'There is an error in payment, please contact support!',
            '500'
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
