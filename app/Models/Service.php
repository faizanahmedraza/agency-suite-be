<?php

namespace App\Models;

use App\Http\Traits\UserAuditTrait;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use UserAuditTrait, CascadeSoftDeletes;

    const CATALOG_STATUS = ['pending' => 0, 'active' => 1];

    const STATUS = ['pending' => 0, 'active' => 1,'blocked' => 2];

    const SUBSCRIPTION_TYPES = ['one-off' => 0,'recurring' => 1];

    protected $cascadeDeletes = ['intakes', 'priceTypes'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'image', 'subscription_type', 'catalog_status','agency_id','status'
    ];

    public function agency()
    {
        return $this->belongsTo(Agency::class, 'agency_id', 'id');
    }

    public function intakes()
    {
        return $this->hasOne(ServiceIntake::class, 'service_id', 'id');
    }

    public function priceTypes()
    {
        return $this->hasOne(ServicePriceType::class, 'service_id', 'id');
    }

    public function serviceRequests()
    {
        return $this->hasMany(CustomerServiceRequest::class, 'service_id', 'id');
    }
}
