<?php

namespace App\Http\Services\V1\Agency;

use App\Models\Transaction;
use Illuminate\Http\Request;

use App\Helpers\TimeStampHelper;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\V1\ModelException;
use App\Exceptions\V1\FailureException;

class TransactionService
{
    public static function create($data,$type = 'bank')
    {
        $transaction = new Transaction();
        $transaction->customer_id = $data->customer_id;
        $transaction->agency_id = app('agency')->id;
        $transaction->invoice_id = $data->id;
        $transaction->card_id = $data->card_id;
        $transaction->type = Transaction::TYPE[$type];
        $transaction->save();

        if (!$transaction) {
            throw FailureException::serverError();
        }

        return $transaction;
    }


}
