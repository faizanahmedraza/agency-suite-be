<?php

namespace App\Http\Controllers\V1\Agency;

use App\Http\Businesses\V1\Agency\CustomerBusiness;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Agency\CustomerRequest;
use App\Http\Requests\V1\Agency\UserListRequest;
use App\Http\Resources\SuccessResponse;
use App\Http\Resources\V1\Agency\CustomersResponse;
use App\Http\Resources\V1\Agency\CustomerResponse;
use Illuminate\Support\Facades\DB;

/**
 * @group Agency Customers
 * @authenticated
 */
class CustomerController extends Controller
{
    private $module;

    public function __construct()
    {
        $this->module = 'agency_customers';
        $ULP = '|' . $this->module . '_all|agency_access_all'; //UPPER LEVEL PERMISSIONS
        $this->middleware('permission:' . $this->module . '_read' . $ULP, ['only' => ['first', 'get']]);
        $this->middleware('permission:' . $this->module . '_create' . $ULP, ['only' => ['store']]);
        $this->middleware('permission:' . $this->module . '_update' . $ULP, ['only' => ['update']]);
        $this->middleware('permission:' . $this->module . '_delete' . $ULP, ['only' => ['destroy']]);
    }

    /**
     * Customers List
     * This api return the collection of all Agency Customers.
     *
     * @header Authorization String required Example: Bearer TOKEN
     *
     * @urlParam customers string 1,2,3,4
     * @urlParam email string ex: abc.com,xyz.co
     * @urlParam first_name string
     * @urlParam last_name string
     * @urlParam full_name string
     * @urlParam status string ex: pending,active,blocked
     * @urlParam order_by string ex: asc/desc
     * @urlParam from_date string Example: Y-m-d
     * @urlParam to_date string Example: Y-m-d
     * @urlParam pagination boolean
     * @urlParam page_limit integer
     * @urlParam page integer
     *
     * @responseFile 200 responses/V1/Agency/CustomersResponse.json
     * @responseFile 422 responses/ValidationResponse.json
     */

    public function get(UserListRequest $request)
    {
        $customers = CustomerBusiness::get($request);
        return (new CustomersResponse($customers));
    }

    /**
     * Create Customer
     * This api is for create new Customer
     *
     * @header Authorization String required Example: Bearer TOKEN
     *
     * @bodyParam first_name String required
     * @bodyParam last_name String required
     * @bodyParam email array required
     *
     * @responseFile 200 responses/V1/Agency/CustomerResponse.json
     * @responseFile 422 responses/ValidationResponse.json
     */

    public function store(CustomerRequest $request)
    {
        DB::beginTransaction();
        $customer = CustomerBusiness::store($request);
        DB::commit();
        return (new CustomerResponse($customer));
    }

    /**
     * Customer Details
     * This api show the details of requested Customer.
     *
     * @header Authorization String required Example: Bearer TOKEN
     *
     * @urlParam id required Integer
     *
     * @responseFile 200 responses/V1/Agency/CustomerResponse.json
     * @responseFile 422 responses/ValidationResponse.json
     *
     */

    public function first(int $id)
    {
        $customer = CustomerBusiness::first($id);
        return (new CustomerResponse($customer));
    }

    /**
     * Customer Update
     * This api update the details of requested Customer.
     *
     * @header Authorization String required Example: Bearer TOKEN
     *
     * @urlParam id required Integer
     *
     * @bodyParam first_name String required
     * @bodyParam last_name String required
     *
     * @responseFile 200 responses/V1/Agency/CustomersResponse.json
     * @responseFile 422 responses/ValidationResponse.json
     *
     */

    public function update(CustomerRequest $request, $id)
    {
        DB::beginTransaction();
        $customer = CustomerBusiness::update($request, $id);
        DB::commit();
        return (new CustomerResponse($customer));
    }

    /**
     * Delete Customer
     *
     * Delete Requested Record
     *
     * @header Authorization String required Example: Bearer TOKEN
     *
     * @urlParam id required Integer
     *
     * @responseFile 200 responses/SuccessResponse.json
     * @responseFile 422 responses/ValidationResponse.json
     *
     * */

    public function destroy(int $id)
    {
        CustomerBusiness::destroy($id);
        return new SuccessResponse([]);
    }

    /**
     * Toggle Customer Status
     * This api update the customers status to active or deactive
     *
     * @urlParam id integer required
     *
     * @responseFile 200 responses/SuccessResponse.json
     * @responseFile 401 responses/UnAuthorizedResponse.json
     */

    public static function toggleStatus(int $id)
    {
        CustomerBusiness::toggleStatus($id);
        return new SuccessResponse([]);
    }
}
