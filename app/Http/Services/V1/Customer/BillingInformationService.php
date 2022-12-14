<?php

namespace App\Http\Services\V1\Customer;

use App\Exceptions\V1\ModelException;
use App\Exceptions\V1\FailureException;
use App\Http\Wrappers\StripeWrapper;
use App\Models\CustomerCardDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillingInformationService
{
    public static function get()
    {
        return CustomerCardDetail::with(['customer', 'agency'])->where('agency_id', app('agency')->id)->where('customer_id', \auth()->id())->orderBy('id')->get();
    }

    public static function store(Request $request)
    {
        $paymentGateway = PaymentGatewayService::first('stripe');
        $customerPaymentGateway = CustomerPaymentGatewayService::first($paymentGateway->id);
        if (empty($customerPaymentGateway)) {
            $stripeCustomer = StripeWrapper::createCustomer();
            $createCustomerRequest = new Request();
            $createCustomerRequest->payment_gateway_id = $paymentGateway->id;
            $createCustomerRequest->customer_key = $stripeCustomer['id'];
            $createCustomerRequest->default = true;
            $customerPaymentGateway = CustomerPaymentGatewayService::create($createCustomerRequest);
        }
        $stripeCard = StripeWrapper::createCard($request, $customerPaymentGateway->customer_key);
        $billing = new CustomerCardDetail;
        $billing->agency_id = app('agency')->id;
        $billing->customer_id = Auth::id();
        $billing->holder_name = trim($request->holder_name);
        $billing->last_digits = trim(substr($request->card_no, -4));
        $billing->exp_month = trim($request->expiry_month);
        $billing->exp_year = trim($request->expiry_year);
        $billing->address = trim($request->address);
        $billing->country = trim($request->country);
        $billing->city = trim($request->city);
        $billing->state = trim($request->state);
        $billing->street = trim($request->street);
        $billing->zip_code = trim($request->zip_code);
        $billing->card_id = $stripeCard['id'];
        $billing->brand = $stripeCard['brand'];
        $billing->customer_payment_gateway_id = $customerPaymentGateway->id;
        $billing->created_by = auth()->id();
        if (count(self::get()) > 0) {
            $billing->is_primary = false;
        } else {
            $billing->is_primary = true;
        }
        $billing->save();

        if (!$billing) {
            throw FailureException::serverError();
        }
        return $billing;
    }

    public static function first($id, $with = ['customer', 'agency'])
    {
        return CustomerCardDetail::with($with)->where('id', $id)->where('agency_id', app('agency')->id)->where('customer_id', \auth()->id())->first();
    }

    public static function destroy(CustomerCardDetail $billing)
    {
        if (!empty($billing->card_id)) {
            $paymentGateway = PaymentGatewayService::first('stripe');
            $customerPaymentGateway = CustomerPaymentGatewayService::first($paymentGateway->id);
            StripeWrapper::deleteCard($billing, $customerPaymentGateway->customer_key);
        }
        $billing->delete();
    }

    public static function makePrimary(CustomerCardDetail $billing)
    {
        $billing->is_primary = true;
        $billing->save();
        CustomerCardDetail::where('id', '!=', $billing->id)->where('agency_id', app('agency')->id)->where('customer_id', auth()->id())->update(['is_primary' => false]);
    }
}
