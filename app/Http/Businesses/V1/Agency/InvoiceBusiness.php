<?php

namespace App\Http\Businesses\V1\Agency;

use App\Http\Services\V1\Agency\AgencyBusinessService;
use App\Http\Services\V1\Agency\CustomerInvoiceService;
use App\Http\Services\V1\Agency\CustomerServiceRequestService;
use App\Http\Services\V1\Agency\TransactionService;
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

    public static function store(Request $request)
    {
        $data = $request->all();
        $service = AgencyBusinessService::first($data['service_id']);
        $customerServiceRequest = CustomerServiceRequestService::create($data, $service);
        $invoice = CustomerInvoiceService::create($customerServiceRequest,$service);
        $transaction = TransactionService::create($invoice, 'card');
    }

    public static function destroy($id)
    {
        $invoice = self::first($id);
        CustomerInvoiceService::destroy($invoice);
    }

    public static function changeStatus($id)
    {
        $invoice = self::first($id);
        CustomerInvoiceService::changeStatus($invoice);
    }

    public static function invoicePaid(Request $request)
    {
        CustomerInvoiceService::invoicePaid($request);
    }
}
