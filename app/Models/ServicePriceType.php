<?php

namespace App\Models;

use App\Http\Traits\UserAuditTrait;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Model;

class ServicePriceType extends Model
{
    use UserAuditTrait;

    protected $table = "service_price_types";
}
