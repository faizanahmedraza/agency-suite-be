<?php

namespace App\Http\Controllers\V1\Agency;

use App\Http\Businesses\V1\Agency\ServiceBusiness;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Agency\CatalogServiceListRequest;
use App\Http\Resources\V1\Agency\ServiceResponse;
use App\Http\Resources\V1\Agency\ServicesResponse;

/**
 * @group Agency Services Catalog
 * @authenticated
 */
class CatalogController extends Controller
{
    /**
     * Get Services
     * This api return services collection.
     *
     * @header Domain string required
     *
     * @urlParam services string 1,2,3,4
     * @urlParam name string ex: my service
     * @urlParam service_type string ex: one-off,recurring
     * @urlParam status string required ex: active
     * @urlParam catalog_status required string ex: active
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

    public function getServices(CatalogServiceListRequest $request)
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
    public function getService(int $id)
    {
        $service = ServiceBusiness::first($id);
        return (new ServiceResponse($service));
    }
}
