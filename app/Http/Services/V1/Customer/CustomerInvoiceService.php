<?php

namespace App\Http\Services\V1\Customer;

use App\Exceptions\V1\ModelException;
use App\Http\Businesses\V1\Customer\BillingInformationBusiness;
use App\Http\Businesses\V1\Customer\RequestServiceBusiness;
use App\Http\Businesses\V1\Customer\TransactionBusiness;
use App\Http\Wrappers\StripeWrapper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\CustomerInvoice;
use App\Helpers\TimeStampHelper;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\V1\FailureException;

class CustomerInvoiceService
{
    public static function create($data, $service)
    {
        $type = $data->recurring_type;
        $customerInvoice = new CustomerInvoice();
        $customerInvoice->agency_id = app('agency')->id;
        $customerInvoice->customer_service_request_id = $data->id;
        $customerInvoice->amount = $service->priceTypes->price;
        if ($service->subscription_type == 1) {
            $customerInvoice->amount = $service->priceTypes->$type;
        }
        $customerInvoice->customer_id = Auth::id();
        $customerInvoice->is_paid = 0;
        $customerInvoice->created_by = auth()->id();
        $customerInvoice->save();

        if (!$customerInvoice) {
            throw FailureException::serverError();
        }

        return $customerInvoice;
    }

    public static function get(Request $request)
    {
        $invoices = CustomerInvoice::query()->with(['agency', 'serviceRequest', 'serviceRequest.service']);

        if ($request->query("is_paid")) {
            $invoices->where('is_paid', trim(strtolower($request->is_paid)));
        }

        if ($request->query('order_by')) {
            $invoices->orderBy('id', $request->get('order_by'));
        } else {
            $invoices->orderBy('id', 'desc');
        }

        if ($request->query('from_date')) {
            $from = TimeStampHelper::formateDate($request->from_date);
            $invoices->whereDate('created_at', '>=', $from);
        }

        if ($request->query('to_date')) {
            $to = TimeStampHelper::formateDate($request->to_date);
            $invoices->whereDate('created_at', '<=', $to);
        }

        $invoices->where('agency_id', app('agency')->id)->where('customer_id', \auth()->id());

        return ($request->filled('pagination') && $request->get('pagination') == 'false')
            ? $invoices->get()
            : $invoices->paginate(\pageLimit($request));
    }

    public static function first($id, $with = ['agency', 'serviceRequest', 'serviceRequest.service', 'serviceRequest.service.priceTypes'], $bypass = false)
    {
        $invoice = CustomerInvoice::with($with)->where('id', $id)->where('agency_id', app('agency')->id)->where('customer_id', \auth()->id())->first();
        if (!$invoice && !$bypass) {
            throw ModelException::dataNotFound();
        }
        return $invoice;
    }

    public static function destroy(CustomerInvoice $invoice)
    {
        $invoice->delete();
    }

    public static function invoicePaid(Request $request)
    {
        $cardDetail = BillingInformationBusiness::first($request->card_id);
        if (isset($request->invoice_id) && !is_null($request->invoice_id)) {
            $invoice = self::first($request->invoice_id, [], true);
            $paymentGateway = PaymentGatewayService::first('stripe');
            $customerPaymentGateway = CustomerPaymentGatewayService::first($paymentGateway->id);

            $chargeRequest = new Request();
            $chargeRequest->amount = (int)$invoice->amount * 100;
            $chargeRequest->customer_key = $customerPaymentGateway->customer_key;
            $chargeRequest->card_id = $cardDetail->card_id;
            $chargeRequest->description = "You have successfully purchases a service.";
            StripeWrapper::charge($chargeRequest);

            $invoice->is_paid = true;
            $invoice->paid_by = "customer";
            $invoice->paid_at = Carbon::now()->toDateTimeString();
            $invoice->updated_by = auth()->id();
            $invoice->save();

            $serviceRequest = new Request();
            $serviceRequest->replace(['status' => 'active']);
            RequestServiceBusiness::changeStatus($invoice->customer_service_request_id, $serviceRequest);
            $transacData = new \stdClass();
            $transacData->id = $invoice->id;
            $transacData->card_id = $cardDetail->id;
            $transaction = TransactionBusiness::create($transacData, 'card');
        }
    }
}
