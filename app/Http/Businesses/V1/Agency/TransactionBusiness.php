<?php

namespace App\Http\Businesses\V1\Agency;

use App\Http\Services\V1\Agency\TransactionService;

class TransactionBusiness
{
    public static function create($data,$type='bank')
    {
        return TransactionService::create($data,$type);
    }
}
