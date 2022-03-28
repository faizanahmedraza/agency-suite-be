<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerBillingInformation extends Model
{
    use SoftDeletes;

    protected $table = "customer_billing_information";

    protected $fillable = [
        'invoice_to',
        'address',
        'country',
        'city',
        'state',
        'zip_code',
        'tax_code',
        'agency_id',
        'customer_id',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class,'customer_id','user_id');
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class,'agency_id','id');
    }
}
