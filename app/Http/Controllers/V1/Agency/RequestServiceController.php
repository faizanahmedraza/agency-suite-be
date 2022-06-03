<?php

namespace App\Http\Controllers\V1\Agency;

use App\Http\Controllers\Controller;
use App\Http\Businesses\V1\Agency\RequestServiceBusiness;
use App\Http\Requests\V1\Agency\RequestServiceChangeStatusRequest;
use App\Http\Requests\V1\Agency\RequestServiceRequest;
use App\Http\Resources\SuccessResponse;
use App\Http\Resources\V1\Agency\CustomersServiceRequestResponse;
use App\Http\Requests\V1\Agency\CustomerRequestServiceListRequest;
use App\Http\Resources\V1\Agency\CustomersServiceRequestListResponse;
use Illuminate\Support\Facades\DB;

/**
 * @group Agency Services Requests
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
        $this->middleware('permission:' . $this->module . '_update' . $ULP, ['only' => ['update']]);
        $this->middleware('permission:' . $this->module . '_delete' . $ULP, ['only' => ['destroy']]);
    }

    /**
     * Get Requested Services List
     *
     * @header Domain string required
     *
     * @urlParam status string pending,submitted
     * @urlParam customer_id integer
     * @urlParam title string ex: abc,xyz
     * @urlParam order_by string ex: asc/desc
     * @urlParam from_date string Example: Y-m-d
     * @urlParam to_date string Example: Y-m-d
     * @urlParam pagination boolean
     * @urlParam page_limit integer
     * @urlParam page integer
     *
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
     * Request Service
     *
     * @header Domain string required
     *
     * @bodyParam service_id integer required
     * @bodyParam customer_id integer required
     * @bodyParam recurring_type string optional if one-off Example : weekly,monthly,quarterly,biannually,annually
     * @bodyParam intake_form array required Example :{key1:value1,key2:value2}
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
     * Request Service Change Status
     *
     * @header Domain string required
     *
     * @urlParam  id required Integer
     * @bodyParam status required string ex: pending,active,hold,completed
     *
     * @responseFile 200 responses/SuccessResponse.json
     * @responseFile 422 responses/ValidationResponse.json
     */
    public function changeStatus(RequestServiceChangeStatusRequest $request,$id)
    {
        DB::beginTransaction();
        RequestServiceBusiness::changeStatus($request,$id);
        DB::commit();
        return new SuccessResponse([]);
    }
}
