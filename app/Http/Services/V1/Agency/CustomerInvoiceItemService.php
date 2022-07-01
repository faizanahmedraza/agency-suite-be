<?php

namespace App\Http\Services\V1\Agency;

use App\Http\Businesses\V1\Agency\BillingInformationBusiness;
use App\Http\Businesses\V1\Agency\RequestServiceBusiness;
use App\Http\Businesses\V1\Agency\TransactionBusiness;
use App\Http\Wrappers\StripeWrapper;
use App\Models\CustomerInvoiceItem;
use App\Models\CustomerServiceRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\CustomerInvoice;

use App\Helpers\TimeStampHelper;
use App\Exceptions\V1\ModelException;
use App\Exceptions\V1\FailureException;

class CustomerInvoiceItemService
{
    public static function createMany($invoice, Request $request)
    {
        $invoiceItems = [];
        $netTotalAmount = 0;
        foreach ($request->invoice_items as $item) {
            $discount = empty($item['discount']) ? 0 : $item['discount'];
            $grossAmount = $item['rate'] * $item['quantity'];
            $netAmount = $grossAmount - $discount;
            $netTotalAmount += $netAmount;
            $invoiceItems[] = new CustomerInvoiceItem([
                'name' => $item['name'],
                'rate' => $item['rate'],
                'quantity' => (int)$item['quantity'],
                'discount' => (int)$discount,
                'gross_amount' => floatval($grossAmount),
                'net_amount' => floatval($netAmount),
                'agency_id' => app('agency')->id,
                'customer_id' => $request->customer_id,
            ]);
        }
        $invoice->invoiceItems()->saveMany($invoiceItems);

        return $netTotalAmount;
    }
}
