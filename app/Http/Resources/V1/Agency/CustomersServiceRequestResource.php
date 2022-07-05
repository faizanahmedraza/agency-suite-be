<?php

namespace App\Http\Resources\V1\Agency;

use Illuminate\Http\Resources\Json\JsonResource as Resource;
use App\Models\CustomerServiceRequest;

class CustomersServiceRequestResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $status = array_flip(CustomerServiceRequest::STATUS);
        $intakeForm = json_decode($this->intake_form, true) ?? [];
        return [
            'id' => $this->id ?? '',
            'service' => new ServiceResource($this->whenLoaded('service')),
            'customer' => new CustomerResource($this->customer->user),
            'is_recurring' => $this->is_recurring ?? '',
            'recurring_type' => $this->recurring_type ?? '',
            'quantity' => $this->quantity ?? '',
            'status' => $status[$this->status] ?? '',
            'intake_form' => array_map('mapDescriptionUnSerialize',$intakeForm),
            'invoice' => $this->whenLoaded('invoice') ?? '',
            'created_at' => $this->created_at ?? '',
            'next_recurring_date' => $this->next_recurring_date ?? '',
        ];
    }
}
