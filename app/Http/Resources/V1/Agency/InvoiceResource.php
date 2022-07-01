<?php

namespace App\Http\Resources\V1\Agency;

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
            'customer' => new CustomerResource($this->customer->user),
            'invoice_number' => "INV-".substr('0000000'.$this->id,-7) ?? "",
            'customer_service_request' => new CustomersServiceRequestResource($this->whenLoaded('serviceRequest')),
            'invoice_items' => InvoiceItemResource::collection($this->whenLoaded('invoiceItems')),
            'is_paid' => $this->is_paid ? "yes" : "no",
            'paid_by' => $this->paid_by ?? "",
            'paid_at' => $this->paid_at ?? "",
            'discount' =>  $this->discount ?? "",
            'gross_amount' =>  $this->gross_amount ?? "",
            'amount' =>  $this->amount ?? "",
            'created_at' =>  $this->created_at ?? "",
        ];
    }
}
