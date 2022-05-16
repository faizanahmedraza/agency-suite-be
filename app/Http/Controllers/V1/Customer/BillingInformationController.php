<?php

namespace App\Http\Controllers\V1\Customer;

use App\Http\Businesses\V1\Customer\BillingInformationBusiness;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Customer\BillingInformationRequest;
use App\Http\Resources\SuccessResponse;
use App\Http\Resources\V1\Customer\BillingInformationResponse;
use Illuminate\Support\Facades\DB;

/**
 * @group Customer Billing Information
 * @authenticated
 */
class BillingInformationController extends Controller
{
    private $module;

    public function __construct()
    {
        $this->module = 'agency_customers_billing_information';
        $ULP = '|' . $this->module . '_all'; //UPPER LEVEL PERMISSIONS
        $this->middleware('permission:' . $this->module . '_read' . $ULP, ['only' => ['first']]);
        $this->middleware('permission:' . $this->module . '_create' . $ULP, ['only' => ['store']]);
        $this->middleware('permission:' . $this->module . '_update' . $ULP, ['only' => ['update']]);
        $this->middleware('permission:' . $this->module . '_delete' . $ULP, ['only' => ['destroy']]);
    }

    /**
     * Create Billing Information
     * This api create new billing information.
     *
     * @header Domain string required
     *
     * @bodyParam  address string required
     * @bodyParam  country string required
     * @bodyParam  city string required
     * @bodyParam  state string required
     * @bodyParam  zip_code integer required
     *
     * @responseFile 200 responses/V1/Customer/BillingInformationResponse.json
     * @responseFile 422 responses/ValidationResponse.json
     * @responseFile 401 responses/UnAuthorizedResponse.json
     */

    public function store(BillingInformationRequest $request)
    {
        DB::beginTransaction();
        $billing = BillingInformationBusiness::store($request);
        DB::commit();
        return (new BillingInformationResponse($billing));
    }

    /**
     * Show Billing Information
     * This api show the billing information details.
     *
     * @header Domain string required
     *
     * @responseFile 200 responses/V1/Customer/BillingInformationResponse.json
     */
    public function first()
    {
        $billing = BillingInformationBusiness::first();
        return (new BillingInformationResponse($billing));
    }

    /**
     * Update Billing Information
     * This api update billing information.
     *
     * @header Domain string required
     *
     * @bodyParam  invoice_to string required
     * @bodyParam  address string required
     * @bodyParam  country string required
     * @bodyParam  city string required
     * @bodyParam  state string required
     * @bodyParam  zip_code integer required
     * @bodyParam  tax_code string optional
     *
     * @responseFile 200 responses/V1/Customer/BillingInformationResponse.json
     * @responseFile 422 responses/ValidationResponse.json
     * @responseFile 401 responses/UnAuthorizedResponse.json
     */

    public function update(BillingInformationRequest $request)
    {
        DB::beginTransaction();
        $billing = BillingInformationBusiness::update($request);
        DB::commit();
        return (new BillingInformationResponse($billing));
    }

    /**
     * Delete Billing Information
     *
     * This api delete billing information
     *
     * @header Domain string required
     *
     * @responseFile 200 responses/SuccessResponse.json
     * @responseFile 401 responses/UnAuthorizedResponse.json
     */

    public function destroy()
    {
        BillingInformationBusiness::destroy();
        return new SuccessResponse([]);
    }
}
