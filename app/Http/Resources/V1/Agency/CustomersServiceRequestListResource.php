<?php

namespace App\Http\Resources\V1\Agency;

use Illuminate\Http\Resources\Json\JsonResource as Resource;
use App\Models\CustomerServiceRequest;

class CustomersServiceRequestListResource extends Resource
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
        return [
            'id' => $this->id ?? '',
            'service_name' => optional($this->service)->name ?? '',
            'customer_name' => optional($this->customer->user)->full_name ?? '',
            'status' =>$status[$this->status] ?? '',
            'created_at' => $this->created_at ?? '',
        ];
    }
}
