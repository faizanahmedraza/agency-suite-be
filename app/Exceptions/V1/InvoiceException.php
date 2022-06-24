<?php

namespace App\Exceptions\V1;

use App\Exceptions\BaseException;

class InvoiceException extends BaseException
{
    public static function customMsg($msg,$code=422): self
    {
        return new self(
            $msg,
            $code
        );
    }
}
