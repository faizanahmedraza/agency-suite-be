<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerServiceRequest extends Model
{
    use  SoftDeletes;

    protected $table = "customer_service_requests";

    // const STATUS = ['pending' => 0, 'submitted' => 1 ,'completed' => 2];
    const STATUS = ['submitted' => 0, 'completed' => 1 ];


    const RECURRING_TYPE=['weekly','monthly','quarterly','biannually','annually'];

    public function agency()
    {
        return $this->belongsTo(Agency::class, 'agency_id', 'id');
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'user_id');
    }
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'id');
    }

    public function invoices()
    {
        return $this->hasMany(CustomerInvoice::class, 'customer_service_request_id', 'id');
    }
}
