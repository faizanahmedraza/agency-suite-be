<?php

namespace App\Http\Resources\V1\Customer;

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
            'service' => new ServiceResource($this->whenLoaded('service')),
            'is_recurring' => $this->is_recurring ?? '',
            'recurring_type' => $this->recurring_type ?? '',
            'status' =>$status[$this->status] ?? '',
            'quantity' => $this->quantity ?? '',
            'intake_form' => $intakeForm,
            'invoice' => $this->whenLoaded('invoice') ?? '',
            'created_at' => $this->created_at ?? '',
        ];
    }
}
