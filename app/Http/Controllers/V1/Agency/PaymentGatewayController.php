<?php

namespace App\Http\Controllers\V1\Agency;

use App\Http\Businesses\V1\Agency\PaymentGatewayBusiness;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Agency\PaymentGatewayRequest;
use App\Http\Resources\SuccessResponse;
use App\Http\Resources\V1\Agency\PaymentGatewayResponse;

/**
 * @group Agency Payment Gateway
 * @authenticated
 */
class PaymentGatewayController extends Controller
{
//    private $module;
//
//    public function __construct()
//    {
//        $this->module = 'agency_customers_billing_information';
//        $ULP = '|' . $this->module . '_all|agency_access_all'; //UPPER LEVEL PERMISSIONS
//        $this->middleware('permission:' . $this->module . '_read' . $ULP, ['only' => ['get']]);
//    }

    /**
     * Get Payment Gateway
     *
     * @header Domain string required
     *
     * @urlParam gateway string required ex: stripe/paypal
     *
     * @responseFile 200 responses/V1/Agency/PaymentGatewayResponse.json
     * @responseFile 422 responses/ValidationResponse.json
     */
    public function first($gateway)
    {
        $paymentGateway = PaymentGatewayBusiness::first($gateway);
        return (new PaymentGatewayResponse($paymentGateway));
    }

    /**
     * Create Payment Gateway
     *
     * This api is for create payment gateway
     *
     * @header Domain string required
     *
     * @bodyParam gateway string ex: stripe/paypal
     * @bodyParam gateway_secret string required ex: sc_qeqeqweqwewqewq21321dwdwewq
     *
     * @responseFile 200 responses/V1/Agency/PaymentGatewayResponse.json
     * @responseFile 422 responses/ValidationResponse.json
     */
    public function create(PaymentGatewayRequest $request)
    {
        $paymentGateway = PaymentGatewayBusiness::create($request);
        return (new PaymentGatewayResponse($paymentGateway));
    }

    /**
     * Change Payment Gateway Status
     *
     * This api is for change status
     *
     * @header Domain string required
     *
     * @urlParam gateway string required ex: stripe/paypal
     *
     * @responseFile 200 responses/SuccessResponse.json
     * @responseFile 401 responses/UnAuthorizedResponse.json
     */
    public function changeStatus($gateway)
    {
        PaymentGatewayBusiness::changeStatus($gateway);
        return new SuccessResponse([]);
    }
}
