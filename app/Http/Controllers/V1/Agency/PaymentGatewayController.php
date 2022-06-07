<?php

namespace App\Http\Controllers\V1\Agency;

use App\Http\Businesses\V1\Agency\PaymentGatewayBusiness;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Agency\PaymentGatewayRequest;

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
     * Create Payment Gateway
     *
     * @header Domain string required
     *
     * @bodyParam gateway string required ex: stripe
     *
     * @responseFile 200 responses/SuccessResponse.json
     * @responseFile 422 responses/ValidationResponse.json
     */
    public function create(PaymentGatewayRequest $request)
    {
        $billings = PaymentGatewayBusiness::create($request);
        return (new BillingInformationListResponse($billings));
    }
}
