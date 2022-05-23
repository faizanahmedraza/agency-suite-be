<?php

namespace App\Http\Businesses\V1\Customer;

use App\Http\Services\V1\Customer\TransactionService;

class TransactionBusiness
{
    public static function create($data,$type='bank')
    {
        return TransactionService::create($data,$type);
    }
}
