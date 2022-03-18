<?php

namespace App\Models;

use App\Http\Traits\UserAuditTrait;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Model;

class ServiceIntake extends Model
{
    use UserAuditTrait;

    protected $table = "service_intakes";
}
