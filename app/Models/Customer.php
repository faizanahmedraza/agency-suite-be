<?php

namespace App\Models;

use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes, CascadeSoftDeletes;

    protected $table = "agency_customers";

    protected $cascadeDeletes = ['billingInformation', 'serviceRequests'];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class, 'agency_id', 'id');
    }

    public function billingInformation()
    {
        return $this->hasOne(CustomerBillingInformation::class, 'customer_id', 'user_id');
    }

    public function serviceRequests()
    {
        return $this->hasMany(CustomerServiceRequest::class, 'customer_id', 'user_id');
    }
}
