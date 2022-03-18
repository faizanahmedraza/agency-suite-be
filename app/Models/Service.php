<?php

namespace App\Models;

use App\Http\Traits\UserAuditTrait;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use UserAuditTrait, CascadeSoftDeletes;

    const STATUS = ['pending' => 0, 'active' => 1];

    protected $cascadeDeletes = ['intakes','priceTypes'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'username', 'password', 'status','last_login'
    ];

    public function agency()
    {
        return $this->belongsTo(Agency::class,'agency_id','id');
    }

    public function intakes()
    {
        return $this->hasMany(ServiceIntake::class,'service_id','id');
    }

    public function priceTypes()
    {
        return $this->hasOne(ServicePriceType::class,'service_id','id');
    }
}
