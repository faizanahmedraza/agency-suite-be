<?php

namespace App\Http\Controllers\V1\Agency;

use App\Http\Controllers\Controller;
use App\Http\Businesses\V1\Agency\RequestServiceBuisness;
use App\Http\Resources\V1\Agency\CustomersServiceRequestResponse;
use App\Http\Requests\V1\Agency\CustomerRequestServiceListRequest;
use App\Http\Resources\V1\Agency\CustomersServiceRequestListResponse;

/**
 * @group Agency Request Service
 * @authenticated
 */
class RequestServiceController extends Controller
{

    private $module;

    public function __construct()
    {
        $this->module = 'agency_services_request';
        $ULP = '|' . $this->module . '_all'; //UPPER LEVEL PERMISSIONS
        $this->middleware('permission:' . $this->module . '_read' . $ULP, ['only' => ['first','get']]);
//        $this->middleware('permission:' . $this->module . '_create' . $ULP, ['only' => ['create']]);
//        $this->middleware('permission:' . $this->module . '_update' . $ULP, ['only' => ['update']]);
//        $this->middleware('permission:' . $this->module . '_delete' . $ULP, ['only' => ['destroy']]);
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
        $customerServiceRequests =  RequestServiceBuisness::get($request);
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
        $customerServiceRequest=RequestServiceBuisness::first($id);
        return (new CustomersServiceRequestResponse($customerServiceRequest));
    }

}
