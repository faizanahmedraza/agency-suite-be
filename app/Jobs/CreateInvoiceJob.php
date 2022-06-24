<?php

namespace App\Jobs;

use App\Http\Businesses\V1\Agency\InvoiceBusiness;
use App\Http\Services\V1\Agency\AgencyBusinessService;
use App\Http\Services\V1\Agency\CustomerInvoiceService;
use App\Http\Services\V1\Agency\CustomerServiceRequestService;

class CreateInvoiceJob extends Job
{
    protected $serviceRequests;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($serviceRequests)
    {
        $this->serviceRequests = $serviceRequests;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $serviceRequestsCount = count($this->serviceRequests);
        if ($serviceRequestsCount > 1) {
            $chunks = $this->serviceRequests->chunk(ceil($serviceRequestsCount / 10));
            foreach ($chunks as $serviceRequests) {
                dispatch(new CreateInvoiceJob($serviceRequests))->onQueue('create_invoice');
            }
        } else {
            $serviceRequest = $this->serviceRequests->first();
            $service = AgencyBusinessService::getServiceById($serviceRequest->service_id);
            $data = new \stdClass();
            $data->id = $serviceRequest->id;
            $data->agency_id = $serviceRequest->agency_id;
            $data->customer_id = $serviceRequest->customer_id;
            $data->recurring_type = $serviceRequest->recurring_type;
            if ($serviceRequest->next_recurring_date == date('Y-m-d') . "00:00:00") {
                CustomerServiceRequestService::UpdateRecurringDateWhere($serviceRequest->id,"");
            }
            CustomerInvoiceService::create($data, $service);
        }
    }
}
