<?php

namespace App\Http\Controllers\V1\Agency;

use App\Http\Businesses\V1\Agency\ServiceBusiness;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Agency\ServiceListRequest;
use App\Http\Requests\V1\Agency\ServiceRequest;
use App\Http\Requests\V1\Agency\ServiceToggleStatusRequest;
use App\Http\Resources\SuccessResponse;
use App\Http\Resources\V1\Agency\ServiceResponse;
use App\Http\Resources\V1\Agency\ServicesResponse;
use Illuminate\Support\Facades\DB;

/**
 * @group Agency Services
 * @authenticated
 */
class ServiceController extends Controller
{
    private $module;

    public function __construct()
    {
        $this->module = 'agency_services';
        $ULP = '|' . $this->module . '_all|agency_access_all'; //UPPER LEVEL PERMISSIONS
        $this->middleware('permission:' . $this->module . '_read' . $ULP, ['only' => ['first', 'get', 'search']]);
        $this->middleware('permission:' . $this->module . '_create' . $ULP, ['only' => ['store']]);
        $this->middleware('permission:' . $this->module . '_update' . $ULP, ['only' => ['update']]);
        $this->middleware('permission:' . $this->module . '_delete' . $ULP, ['only' => ['destroy']]);
        $this->middleware('permission:' . $this->module . '_toggle_status' . $ULP, ['only' => ['toggleStatus']]);
    }

    /**
     * Create Service
     * This api create new service.
     *
     * @header Domain string required
     *
     * @bodyParam  name string required
     * @bodyParam  description string required
     * @bodyParam  image string optional ex: base64imageFile formats: png,jpeg,jpg
     * @bodyParam  subscription_type string required ex: 'one-off' ,'recurring'
     * @bodyParam  price integer required only if subscription_type is one_off ex: 123
     * @bodyParam  purchase_limit integer optional only if subscription_type is one_off ex: 12
     * @bodyParam  weekly integer required only if subscription_type is recurring ex: 123
     * @bodyParam  monthly integer required only if subscription_type is recurring ex: 123
     * @bodyParam  quarterly integer required only if subscription_type is recurring ex: 123
     * @bodyParam  biannually integer required only if subscription_type is recurring ex: 123
     * @bodyParam  annually integer required only if subscription_type is recurring ex: 123
     * @bodyParam  max_concurrent_requests integer optional only if subscription_type is recurring ex: 12
     * @bodyParam  max_requests_per_month integer optional only if subscription_type is recurring ex: 12
     *
     * @responseFile 200 responses/V1/Agency/ServiceResponse.json
     * @responseFile 422 responses/ValidationResponse.json
     * @responseFile 401 responses/UnAuthorizedResponse.json
     */

    public function store(ServiceRequest $request)
    {
        DB::beginTransaction();
        $service = ServiceBusiness::store($request);
        DB::commit();
        return (new ServiceResponse($service));
    }

    /**
     * Get Services
     * This api return services collection.
     *
     * @header Domain string required
     *
     * @urlParam services string 1,2,3,4
     * @urlParam name string ex: my service
     * @urlParam service_type string ex: one-off,recurring
     * @urlParam status string ex: pending,active,blocked
     * @urlParam catalog_status string ex: pending,active
     * @urlParam order_by string ex: asc/desc
     * @urlParam from_date string Example: Y-m-d
     * @urlParam to_date string Example: Y-m-d
     * @urlParam pagination boolean
     * @urlParam page_limit integer
     * @urlParam page integer
     *
     * @responseFile 200 responses/V1/Agency/ServicesResponse.json
     * @responseFile 422 responses/ValidationResponse.json
     */

    public function get(ServiceListRequest $request)
    {
        $services = ServiceBusiness::get($request);
        return (new ServicesResponse($services));
    }

    /**
     * Show Service Details
     * This api show the service details.
     *
     * @header Domain string required
     *
     * @urlParam  id required Integer
     *
     * @responseFile 200 responses/V1/Agency/ServiceResponse.json
     */
    public function first(int $id)
    {
        $service = ServiceBusiness::first($id);
        return (new ServiceResponse($service));
    }


    /**
     * Update Service
     * This api update service.
     *
     * @header Domain string required
     *
     * @bodyParam  name string required
     * @bodyParam  description string required
     * @bodyParam  image string optional ex: base64imageFile formats: png,jpeg,jpg
     * @bodyParam  subscription_type string required ex: 'one-off' ,'recurring'
     * @bodyParam  price integer required only if subscription_type is one_off ex: 123
     * @bodyParam  purchase_limit integer optional only if subscription_type is one_off ex: 12
     * @bodyParam  weekly integer required only if subscription_type is recurring ex: 123
     * @bodyParam  monthly integer required only if subscription_type is recurring ex: 123
     * @bodyParam  quarterly integer required only if subscription_type is recurring ex: 123
     * @bodyParam  biannually integer required only if subscription_type is recurring ex: 123
     * @bodyParam  annually integer required only if subscription_type is recurring ex: 123
     * @bodyParam  max_concurrent_requests integer optional only if subscription_type is recurring ex: 12
     * @bodyParam  max_requests_per_month integer optional only if subscription_type is recurring ex: 12
     *
     * @responseFile 200 responses/V1/Agency/ServiceResponse.json
     * @responseFile 422 responses/ValidationResponse.json
     * @responseFile 401 responses/UnAuthorizedResponse.json
     */

    public function update(ServiceRequest $request, int $id)
    {
        DB::beginTransaction();
        $service = ServiceBusiness::update($request, $id);
        DB::commit();
        return (new ServiceResponse($service));
    }

    /**
     * Delete Service
     *
     * This api delete service
     *
     * @header Domain string required
     *
     * @urlParam  id required Integer
     *
     * @responseFile 200 responses/SuccessResponse.json
     * @responseFile 401 responses/UnAuthorizedResponse.json
     */

    public function destroy(int $id)
    {
        ServiceBusiness::destroy($id);
        return new SuccessResponse([]);
    }

    /**
     * Toggle Service Status
     * This api update the service status.
     *
     * @header Domain string required
     *
     * @urlParam id integer required
     * @bodyParam status required string ex: pending,active,blocked
     *
     * @responseFile 200 responses/SuccessResponse.json
     * @responseFile 401 responses/UnAuthorizedResponse.json
     */

    public static function toggleStatus(int $id,ServiceToggleStatusRequest $request)
    {
        ServiceBusiness::toggleStatus($id,$request);
        return new SuccessResponse([]);
    }

    /**
     * Toggle Service Catalog Status
     * This api update the service catalog status to active or pending.
     *
     * @header Domain string required
     *
     * @urlParam id integer required
     *
     * @responseFile 200 responses/SuccessResponse.json
     * @responseFile 401 responses/UnAuthorizedResponse.json
     */
    public static function toggleCatalogStatus(int $id)
    {
        ServiceBusiness::toggleCatalogStatus($id);
        return new SuccessResponse([]);
    }
}
