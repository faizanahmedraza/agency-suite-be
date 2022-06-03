<?php

namespace App\Http\Controllers\V1\Agency;

use App\Http\Businesses\V1\Agency\BillingInformationBusiness;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\Customer\BillingInformationListResponse;
use Illuminate\Http\Request;

/**
 * @group Agency Customer Billing Information
 * @authenticated
 */
class BillingInformationController extends Controller
{
    private $module;

    public function __construct()
    {
        $this->module = 'agency_customers_billing_information';
        $ULP = '|' . $this->module . '_all|agency_access_all'; //UPPER LEVEL PERMISSIONS
        $this->middleware('permission:' . $this->module . '_read' . $ULP, ['only' => ['get']]);
    }

    /**
     * Show All Billings Information
     * This api show the billing information details.
     *
     * @header Domain string required
     *
     * @urlParam customer_id integer ex:1
     *
     * @responseFile 200 responses/V1/Agency/BillingInformationListResponse.json
     */
    public function get(Request $request)
    {
        $billings = BillingInformationBusiness::get($request);
        return (new BillingInformationListResponse($billings));
    }
}
