<?php

namespace App\Models;

use App\Http\Traits\UserAuditTrait;
use Illuminate\Database\Eloquent\Model;

class CustomerInvoiceItem extends Model
{
    use UserAuditTrait;

    protected $table = "customer_invoice_items";

    protected $guarded = [];

    public function invoice()
    {
        return $this->belongsTo(CustomerInvoice::class, 'invoice_id', 'id');
    }

    public function agency()
    {
        return $this->belongsTo(Agency::class, 'agency_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'user_id');
    }
}
