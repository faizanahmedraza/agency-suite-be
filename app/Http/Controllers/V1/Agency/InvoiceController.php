<?php

namespace App\Http\Controllers\V1\Agency;

use App\Http\Businesses\V1\Agency\InvoiceBusiness;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Agency\CustomerInvoiceRequest;
use App\Http\Requests\V1\Agency\InvoicePaidRequest;
use App\Http\Requests\V1\Agency\InvoiceRequest;
use App\Http\Resources\SuccessResponse;
use App\Http\Resources\V1\Agency\InvoiceResponse;
use App\Http\Resources\V1\Agency\InvoicesResponse;
use Illuminate\Support\Facades\DB;

/**
 * @group Agency Invoices
 * @authenticated
 */
class InvoiceController extends Controller
{
    private $module;

    public function __construct()
    {
        $this->module = 'agency_customer_invoices';
        $ULP = '|' . $this->module . '_all|agency_access_all'; //UPPER LEVEL PERMISSIONS
        $this->middleware('permission:' . $this->module . '_read' . $ULP, ['only' => ['first','get']]);
        $this->middleware('permission:' . $this->module . '_create' . $ULP, ['only' => ['store']]);
        $this->middleware('permission:' . $this->module . '_delete' . $ULP, ['only' => ['destroy']]);
        $this->middleware('permission:' . $this->module . '_status' . $ULP, ['only' => ['changeStatus']]);
        $this->middleware('permission:' . $this->module . '_invoice_paid' . $ULP, ['only' => ['invoicePaid']]);
    }

    /**
     * Show All Invoices
     * This api show the invoice.
     *
     * @header Domain string required
     *
     * @urlParam is_paid boolean ex: true
     * @urlParam order_by string ex: asc/desc
     * @urlParam from_date string Example: Y-m-d
     * @urlParam to_date string Example: Y-m-d
     * @urlParam pagination boolean
     * @urlParam page_limit integer
     * @urlParam page integer
     *
     * @responseFile 200 responses/V1/Agency/InvoicesResponse.json
     */
    public function get(CustomerInvoiceRequest $request)
    {
        $invoices = InvoiceBusiness::get($request);
        return (new InvoicesResponse($invoices));
    }

    /**
     * Show Invoice
     * This api show the invoice details.
     *
     * @header Domain string required
     *
     * @urlParam id integer required
     *
     * @responseFile 200 responses/V1/Agency/InvoiceResponse.json
     */
    public function first($id)
    {
        $invoice = InvoiceBusiness::first($id);
        return (new InvoiceResponse($invoice));
    }

    /**
     * Create Invoice
     * This api create new custom invoice.
     *
     * @header Domain string required
     *
     * @bodyParam invoice_type integer required ex: custom/service
     * @bodyParam service_id integer required if invoice_type = service
     * @bodyParam customer_id integer required
     * @bodyParam recurring_type string optional if one-off Example : weekly,monthly,quarterly,biannually,annually
     * @bodyParam intake_form array required if invoice_type = service Example :{key1:value1,key2:value2}
     * @bodyParam quantity integer required if invoice_type = service Example :2
     * @bodyParam invoice_items array Example :[]
     *
     * @responseFile 200 responses/SuccessResponse.json
     * @responseFile 422 responses/ValidationResponse.json
     * @responseFile 401 responses/UnAuthorizedResponse.json
     */

    public function store(InvoiceRequest $request)
    {
        DB::beginTransaction();
        InvoiceBusiness::store($request);
        DB::commit();
        return new SuccessResponse([]);
    }


    /**
     * Delete Invoice
     *
     * This api delete invoice
     *
     * @header Domain string required
     *
     * @urlParam id integer required
     *
     * @responseFile 200 responses/SuccessResponse.json
     * @responseFile 401 responses/UnAuthorizedResponse.json
     */
    public function destroy($id)
    {
        InvoiceBusiness::destroy($id);
        return new SuccessResponse([]);
    }

    /**
     * Change Invoice Status
     *
     * This api is for change status to paid or unpaid
     *
     * @header Domain string required
     *
     * @urlParam id integer required
     *
     * @responseFile 200 responses/SuccessResponse.json
     * @responseFile 401 responses/UnAuthorizedResponse.json
     */
    public function changeStatus($id)
    {
        InvoiceBusiness::changeStatus($id);
        return new SuccessResponse([]);
    }

    /**
     * Invoice Paid
     *
     * This api is for marking invoice paid
     *
     * @header Domain string required
     *
     * @bodyParam  card_id integer required
     * @bodyParam  invoice_id integer required
     *
     * @responseFile 200 responses/SuccessResponse.json
     * @responseFile 401 responses/UnAuthorizedResponse.json
     */
    public function invoicePaid(InvoicePaidRequest $request)
    {
        DB::beginTransaction();
        InvoiceBusiness::invoicePaid($request);
        DB::commit();
        return new SuccessResponse([]);
    }
}
