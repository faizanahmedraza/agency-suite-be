<?php

namespace App\Http\Resources\V1\Customer;

use Illuminate\Http\Resources\Json\JsonResource as Resource;

class ServicePriceTypeResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'price' => $this->price,
            'purchase_limit' => $this->purchase_limit,
            'weekly' => $this->weekly,
            'monthly' => $this->monthly,
            'quarterly' => $this->quarterly,
            'biannually' => $this->biannually,
            'annually' => $this->annually,
            'max_concurrent_requests' => $this->max_concurrent_requests,
            'max_requests_per_month' => $this->max_requests_per_month,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
