<?php

namespace App\Http\Controllers\V1\Customer;

use App\Http\Businesses\V1\Customer\BillingInformationBusiness;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Customer\BillingInformationRequest;
use App\Http\Resources\SuccessResponse;
use App\Http\Resources\V1\Customer\BillingInformationListResponse;
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
        $ULP = '|' . $this->module . '_all|agency_access_all'; //UPPER LEVEL PERMISSIONS
        $this->middleware('permission:' . $this->module . '_read' . $ULP, ['only' => ['first','get']]);
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
     * @bodyParam  holder_name string required ex: haesw
     * @bodyParam  card_no integer required ex: 1233321321321312
     * @bodyParam  cvc integer required ex: 123
     * @bodyParam  expiry_month integer required ex: 12
     * @bodyParam  expiry_month integer required ex: 20
     * @bodyParam  address string required
     * @bodyParam  country string required
     * @bodyParam  city string required
     * @bodyParam  state string required
     * @bodyParam  street string optional
     * @bodyParam  zip_code integer required ex: 2321
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
     * Show All Billings Information
     * This api show the billing information details.
     *
     * @header Domain string required
     *
     * @responseFile 200 responses/V1/Customer/BillingInformationListResponse.json
     */
    public function get()
    {
        $billings = BillingInformationBusiness::get();
        return (new BillingInformationListResponse($billings));
    }

    /**
     * Show Billing Information
     * This api show the billing information details.
     *
     * @header Domain string required
     *
     * @urlParam id integer required
     *
     * @responseFile 200 responses/V1/Customer/BillingInformationResponse.json
     */
    public function first($id)
    {
        $billing = BillingInformationBusiness::first($id);
        return (new BillingInformationResponse($billing));
    }

    /**
     * Update Billing Information
     * This api update billing information.
     *
     * @header Domain string required
     *
     * @urlParam id integer required
     *
     * @bodyParam  holder_name string required ex: haesw
     * @bodyParam  card_no numeric required ex: 1233321321321312
     * @bodyParam  cvc integer required ex: 123
     * @bodyParam  expiry_month integer required ex: 12
     * @bodyParam  expiry_month integer required ex: 20
     * @bodyParam  address string required
     * @bodyParam  country string required
     * @bodyParam  city string required
     * @bodyParam  state string required
     * @bodyParam  street string optional
     * @bodyParam  zip_code integer required ex: 2321
     *
     * @responseFile 200 responses/V1/Customer/BillingInformationResponse.json
     * @responseFile 422 responses/ValidationResponse.json
     * @responseFile 401 responses/UnAuthorizedResponse.json
     */

    public function update(BillingInformationRequest $request,$id)
    {
        DB::beginTransaction();
        $billing = BillingInformationBusiness::update($request,$id);
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
     * @urlParam id integer required
     *
     * @responseFile 200 responses/SuccessResponse.json
     * @responseFile 401 responses/UnAuthorizedResponse.json
     */

    public function destroy($id)
    {
        BillingInformationBusiness::destroy($id);
        return new SuccessResponse([]);
    }

    /**
     * Delete Billing Information
     *
     * This api make primary card primary
     *
     * @header Domain string required
     *
     * @urlParam id integer required
     *
     * @responseFile 200 responses/SuccessResponse.json
     * @responseFile 401 responses/UnAuthorizedResponse.json
     */
    public function makePrimary($id)
    {
        BillingInformationBusiness::makePrimary($id);
        return new SuccessResponse([]);
    }
}
