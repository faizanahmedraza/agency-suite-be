<?php

namespace App\Http\Resources\V1\Customer;

use Illuminate\Http\Resources\Json\JsonResource as Resource;

class InvoiceResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id ?? "",
            'invoice_number' => "INV-".substr('0000000'.$this->id,-7) ?? "",
            'customer_service_request_id' => new CustomersServiceRequestResource($this->customer_service_request_id),
            'is_paid' => $this->is_paid ?? "",
            'amount' =>  $this->amount ?? "",
            'created_at' =>  $this->created_at ?? "",
        ];
    }
}
