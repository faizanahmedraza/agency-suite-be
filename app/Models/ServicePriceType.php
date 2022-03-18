<?php

namespace App\Models;

use App\Http\Traits\UserAuditTrait;
use Illuminate\Database\Eloquent\Model;

class ServicePriceType extends Model
{
    use UserAuditTrait;

    protected $table = "service_price_types";

    protected $fillable = [
        'price',
        'purchase_limit',
        'weekly',
        'monthly',
        'quarterly',
        'biannually',
        'annually',
        'max_concurrent_requests',
        'max_requests_per_month',
        'service_id',
        'agency_id',
    ];
}
