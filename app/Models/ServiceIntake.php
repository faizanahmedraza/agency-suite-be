<?php

namespace App\Models;

use App\Http\Traits\UserAuditTrait;
use Illuminate\Database\Eloquent\Model;

class ServiceIntake extends Model
{
    use UserAuditTrait;

    protected $table = "service_intakes";

    protected $fillable = [
        'intake',
        'service_id',
        'agency_id',
    ];
}
