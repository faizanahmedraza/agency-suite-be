<?php

namespace App\Models;

use App\Http\Traits\UserAuditTrait;
use Illuminate\Database\Eloquent\Model;

class CustomerPaymentDetail extends Model
{
    use UserAuditTrait;

    protected $table = "customer_card_details";

    protected $guarded = [];

    public function agency()
    {
        return $this->belongsTo(Agency::class, 'agency_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class,'customer_id','user_id');
    }
}
