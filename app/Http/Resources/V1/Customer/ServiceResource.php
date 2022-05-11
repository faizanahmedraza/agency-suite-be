<?php

namespace App\Http\Resources\V1\Customer;

use App\Models\Service;
use Illuminate\Http\Resources\Json\JsonResource as Resource;

class ServiceResource extends Resource
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
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'image' => $this->image,
            'subscription_type' => array_search($this->subscription_type,Service::SUBSCRIPTION_TYPES),
            'catalog_status' => array_search($this->catalog_status, Service::CATALOG_STATUS),
            'price_types' => new ServicePriceTypeResource($this->whenLoaded('priceTypes')),
            'intakes' => new ServiceIntakeResource($this->whenLoaded('intakes')),
            'status' => array_search($this->status, Service::STATUS),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
