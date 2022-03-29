<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerInvoice extends Model
{
    use  SoftDeletes;

    protected $table = "customer_invoices";



    public function agency()
    {
        return $this->belongsTo(Agency::class, 'agency_id', 'id');
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'user_id');
    }

    // public function priceTypes()
    // {
    //     return $this->hasOne(ServicePriceType::class, 'service_id', 'id');
    // }
}
