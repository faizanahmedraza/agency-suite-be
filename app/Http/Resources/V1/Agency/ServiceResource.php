<?php

namespace App\Http\Resources\V1\Agency;

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
            'status' => array_search($this->status, Service::STATUS),
            'price_types' =>  ServicePriceTypeResource::collection($this->whenLoaded('priceTypes')),
            'intakes' =>  ServiceIntakeResource::collection($this->whenLoaded('intakes')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
