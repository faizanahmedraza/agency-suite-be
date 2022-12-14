<?php

namespace App\Http\Businesses\V1\Customer;

use App\Http\Services\V1\Customer\CustomerInvoiceService;
use Illuminate\Http\Request;

class InvoiceBusiness
{
    public static function get(Request $request)
    {
        return CustomerInvoiceService::get($request);
    }

    public static function first($id)
    {
        return CustomerInvoiceService::first($id);
    }

    public static function destroy($id)
    {
        $invoice = self::first($id);
        CustomerInvoiceService::destroy($invoice);
    }

    public static function invoicePaid(Request $request)
    {
        CustomerInvoiceService::invoicePaid($request);
    }
}
