<?php

namespace App\Http\Controllers\V1\Customer;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\SuccessResponse;
use App\Http\Businesses\V1\Customer\CustomerBusiness;
use App\Http\Requests\V1\Customer\RequestServiceRequest;
use App\Http\Businesses\V1\Customer\RequestServiceBuisness;

/**
 * @group Customer Services
 * @authenticated
 */
class RequestServiceController extends Controller
{


    /**
     * Request Service
     * requesting for service
     *
     * @responseFile 200 responses/SuccessResponse.json
     * @responseFile 422 responses/ValidationResponse.json
     */
    public function create(RequestServiceRequest $request)
    {
        DB::beginTransaction();
        RequestServiceBuisness::requestService($request);
        DB::commit();
        return new SuccessResponse([]);
    }


}
