<?php

namespace App\Models;

use App\Http\Traits\UserAuditTrait;
use Illuminate\Database\Eloquent\Model;

class CustomerInvoice extends Model
{
    use UserAuditTrait;

    protected $table = "customer_invoices";


    public function agency()
    {
        return $this->belongsTo(Agency::class, 'agency_id', 'id');
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'user_id');
    }
     public function serviceRequest()
     {
         return $this->belongsTo(CustomerServiceRequest::class, 'customer_service_request_id', 'id');
     }
}
