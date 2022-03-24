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
    /**
     * Create Billing Information
     * This api create new billing information.
     *
     * @bodyParam  invoice_to string required
     * @bodyParam  address string required
     * @bodyParam  country string required
     * @bodyParam  city string required
     * @bodyParam  state string required
     * @bodyParam  zip_code integer required
     * @bodyParam  tax_code string optional
     *
     * @responseFile 200
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
     * @urlParam  id required Integer
     *
     * @responseFile 200
     */
    public function first(int $id)
    {
        $billing = BillingInformationBusiness::first($id);
        return (new BillingInformationResponse($billing));
    }

    /**
     * Update Billing Information
     * This api update billing information.
     *
     * @bodyParam  invoice_to string required
     * @bodyParam  address string required
     * @bodyParam  country string required
     * @bodyParam  city string required
     * @bodyParam  state string required
     * @bodyParam  zip_code integer required
     * @bodyParam  tax_code string optional
     *
     * @responseFile 200
     * @responseFile 422 responses/ValidationResponse.json
     * @responseFile 401 responses/UnAuthorizedResponse.json
     */

    public function update(BillingInformationRequest $request, int $id)
    {
        DB::beginTransaction();
        $billing = BillingInformationBusiness::update($request, $id);
        DB::commit();
        return (new BillingInformationResponse($billing));
    }

    /**
     * Delete Billing Information
     *
     * This api delete billing information
     *
     * @urlParam  id required Integer
     *
     * @responseFile 200 responses/SuccessResponse.json
     * @responseFile 401 responses/UnAuthorizedResponse.json
     */

    public function destroy(int $id)
    {
        BillingInformationBusiness::destroy($id);
        return new SuccessResponse([]);
    }
}
