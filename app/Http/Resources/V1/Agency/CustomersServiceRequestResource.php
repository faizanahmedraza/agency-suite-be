<?php

namespace App\Http\Resources\V1\Agency;

use Illuminate\Http\Resources\Json\JsonResource as Resource;
use App\Models\CustomerServiceRequest;

class CustomersServiceRequestResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $status=array_flip(CustomerServiceRequest::STATUS);
        $intakeForm=json_decode($this->intake_form,true) ?? [];
        return [
            'id' => $this->id ?? '',
            'service_name' => optional($this->service)->name ?? '',
            'customer_name' => optional($this->customer->user)->full_name ?? '',
            'is_recurring' => $this->is_recurring ?? '',
            'recurring_type' => $this->recurring_type ?? '',
            'status' =>$status[$this->status] ?? '',
            'intake_form' => (object)$intakeForm,
            'invoices' => InvoiceResource::collection($this->invoices) ??(object)[],
            'created_at' => $this->created_at ?? '',
        ];
    }
}
