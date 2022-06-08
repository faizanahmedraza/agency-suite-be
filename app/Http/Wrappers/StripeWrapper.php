<?php

namespace App\Http\Wrappers;

use App\Exceptions\V1\PaymentException;
use App\Http\Businesses\V1\Agency\PaymentGatewayBusiness;
use App\Models\CustomerCardDetail;
use Illuminate\Http\Request;

class StripeWrapper
{
    public static function initStripe($gateway = "stripe")
    {
        $secretKey = PaymentGatewayBusiness::first($gateway, false)->gateway_secret ?? env('STRIPE_SECRET');
        return new \Stripe\StripeClient($secretKey);
    }

    public static function createCustomer()
    {
        $stripe = self::initStripe();
        try {
            $response = $stripe->customers->create([
                'name' => auth()->user()->full_name,
                'email' => auth()->user()->username,
            ]);
            if (empty($response)) {
                throw PaymentException::stripeError();
            }
            return $response->toArray();
        } catch (\Exception $e) {
            error_log("Error occurred, " . $e->getMessage(), 0);
        }
    }

    public static function generateToken(Request $request)
    {
        $stripe = self::initStripe();
        try {
            $response = $stripe->tokens->create([
                "card" => [
                    "number" => $request->card_no,
                    "exp_month" => $request->expiry_month,
                    "exp_year" => $request->expiry_year,
                    "cvc" => $request->cvc,
                    "name" => $request->holder_name
                ]
            ]);
            if (empty($response)) {
                throw PaymentException::stripeError();
            }
            return $response->toArray();
        } catch (\Exception $e) {
            error_log("Error occurred, " . $e->getMessage(), 0);
        }
    }

    public static function createCard(Request $request, $customer_key)
    {
        $stripe = self::initStripe();
        $token = self::generateToken($request);

        try {
            $response = $stripe->customers->createSource(
                $customer_key,
                ['source' => $token['id']]
            );
            if (empty($response)) {
                throw PaymentException::stripeError();
            }
            return $response->toArray();
        } catch (\Exception $e) {
            error_log("Error occurred, " . $e->getMessage(), 0);
        }
    }

    public static function deleteCard(CustomerCardDetail $card, $customer_key)
    {
        $stripe = self::initStripe();
        try {
            $response = $stripe->customers->deleteSource(
                $customer_key,
                $card->card_id,
                []
            );
            if (empty($response)) {
                throw PaymentException::stripeError();
            }
        } catch (\Exception $e) {
            error_log("Error occurred, " . $e->getMessage(), 0);
        }
    }

    public static function charge(Request $request)
    {
        $stripe = self::initStripe();
        try {
            $response = $stripe->charges->create([
                "amount" => (int)$request->amount,
                "capture" => true,
                "currency" => "usd",
                'customer' => $request->customer_key,
                "source" => $request->card_id,
                "description" => $request->description
            ]);
            if (empty($response)) {
                throw PaymentException::stripeError();
            }
        } catch (\Exception $e) {
            error_log("Error occurred, " . $e->getMessage(), 0);
        }
    }
}