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
            'service' => new ServiceResource($this->service),
            'customer' => new CustomerResource($this->customer->user),
            'status' =>$status[$this->status] ?? '',
            'created_at' => $this->created_at ?? '',
        ];
    }
}
