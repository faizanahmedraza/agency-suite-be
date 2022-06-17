<?php

namespace App\Models;

use App\Http\Traits\UserAuditTrait;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Model;

class CustomerServiceRequest extends Model
{
    use UserAuditTrait,CascadeSoftDeletes;

    protected $table = "customer_service_requests";

    const STATUS = ['pending' => 0, 'active' => 1, 'hold' => 2, 'completed' => 3, 'cancelled' => 4];

    const RECURRING_TYPE = ['weekly', 'monthly', 'quarterly', 'biannually', 'annually'];

    protected $cascadeDeletes = ['customerCardDetails', 'serviceRequests'];

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

    public function invoice()
    {
        return $this->hasOne(CustomerInvoice::class, 'customer_service_request_id', 'id');
    }
}
