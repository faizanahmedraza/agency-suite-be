<?php

namespace App\Models;

use App\Http\Traits\UserAuditTrait;
use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    use UserAuditTrait;

    protected $table = "payment_gateways";

    protected $guarded = [];

    public function agency()
    {
        return $this->belongsTo(Agency::class, 'agency_id', 'id');
    }
}
