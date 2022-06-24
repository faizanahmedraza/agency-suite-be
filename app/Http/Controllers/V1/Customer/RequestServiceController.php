<?php

namespace App\Http\Controllers\V1\Customer;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\SuccessResponse;
use App\Http\Requests\V1\Customer\RequestServiceRequest;
use App\Http\Businesses\V1\Customer\RequestServiceBusiness;
use App\Http\Resources\V1\Customer\CustomersServiceRequestResponse;
use App\Http\Requests\V1\Customer\CustomerRequestServiceListRequest;
use App\Http\Resources\V1\Customer\CustomersServiceRequestListResponse;

/**
 * @group Customer Services Request
 * @authenticated
 */
class RequestServiceController extends Controller
{
    private $module;

    public function __construct()
    {
        $this->module = 'agency_services_request';
        $ULP = '|' . $this->module . '_all|agency_access_all'; //UPPER LEVEL PERMISSIONS
        $this->middleware('permission:' . $this->module . '_read' . $ULP, ['only' => ['first','get']]);
        $this->middleware('permission:' . $this->module . '_create' . $ULP, ['only' => ['create']]);
    }

    /**
     * Request Service
     *
     * @header Domain string required
     *
     * @bodyParam service_id integer required
     * @bodyParam recurring_type string required Example : weekly,monthly,quarterly,biannually,annually
     * @bodyParam intake_form array required Example :{key1:value1,key2:value2}
     *
     *
     * @responseFile 200 responses/SuccessResponse.json
     * @responseFile 422 responses/ValidationResponse.json
     */
    public function create(RequestServiceRequest $request)
    {
        DB::beginTransaction();
        RequestServiceBusiness::requestService($request);
        DB::commit();
        return new SuccessResponse([]);
    }

    /**
     * Get Requested Services List
     *
     * @header Domain string required
     *
     * @urlParam status string pending,submitted
     * @urlParam title string ex: abc,xyz
     * @urlParam order_by string ex: asc/desc
     * @urlParam from_date string Example: Y-m-d
     * @urlParam to_date string Example: Y-m-d
     * @urlParam pagination boolean
     * @urlParam page_limit integer
     * @urlParam page integer
     *
     * @responseFile 200 responses/V1/Customer/RequestServiceListResponse.json
     * @responseFile 422 responses/ValidationResponse.json
     *
     */
    public function get(CustomerRequestServiceListRequest $request)
    {
        $customerServiceRequests =  RequestServiceBusiness::get($request);
        return (new CustomersServiceRequestListResponse($customerServiceRequests));
    }
    /**
     * Get Request Service details by Id
     *
     * @header Domain string required
     *
     * @urlParam id integer required
     *
     * @responseFile 200 responses/V1/Customer/RequestServiceResponse.json
     * @responseFile 422 responses/ValidationResponse.json
     */
    public function first($id)
    {
        $customerServiceRequest=RequestServiceBusiness::first($id);
        return (new CustomersServiceRequestResponse($customerServiceRequest));
    }

    /**
     * Cancel Request Service by Id
     *
     * @header Domain string required
     *
     * @urlParam id integer required
     *
     * @responseFile 200 responses/SuccessResponse.json
     * @responseFile 422 responses/ValidationResponse.json
     */
    public function cancelRequest($id)
    {
        RequestServiceBusiness::cancelRequest($id);
        return new SuccessResponse([]);
    }
}
