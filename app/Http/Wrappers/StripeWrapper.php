<?php

namespace App\Http\Wrappers;

use Illuminate\Http\Request;

class StripeWrapper
{
    public static function initStripe()
    {
        return new \Stripe\StripeClient(env('STRIPE_SECRET'));
    }

    public function getCustomer($customer_key)
    {
        $stripe = self::initStripe();
        try {
            $response = $stripe->customers->retrieve(
                $customer_key,
                []
            );
            return $response->toArray();
        } catch (\Exception $e) {
            error_log("Error occurred, ", $e->getMessage());
        }
    }

    public static function createCustomer()
    {
        $stripe = self::initStripe();
        try {
            $response = $stripe->customers->create([
                'name' => auth()->user()->full_name,
                'email' => auth()->user()->username,
            ]);
            return $response->toArray();
        } catch (\Exception $e) {
            error_log("Error occurred, ", $e->getMessage());
        }
    }

    public static function generateToken($request)
    {
        $stripe = self::initStripe();
        try {
            $response = $stripe->tokens->create(array(
                "card" => array(
                    "number" => $request->input('card_number'),
                    "exp_month" => $request->input('exp_month'),
                    "exp_year" => $request->input('exp_year'),
                    "cvc" => $request->input('cvc'),
                    "name" => $request->input('name')
                )
            ));
            return $response->toArray();
        } catch (\Exception $e) {
            error_log("Error occurred, ", $e->getMessage());
        }
    }

    public static function createCard(Request $request, $customer_key = null)
    {
        $stripe = self::initStripe();
        $customer_key = empty($customer_key) ? self::createCustomer()['id'] : $customer_key;
        $token = self::generateToken($request);

        try {
            $response = $stripe->customers->createSource(
                $customer_key,
                ['source' => $token['id']]
            );
            return $response->toArray();
        } catch (\Exception $e) {
            error_log("Error occurred, ", $e->getMessage());
        }
    }
}