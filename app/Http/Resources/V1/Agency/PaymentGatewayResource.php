<?php

namespace App\Http\Resources\V1\Agency;

use Illuminate\Http\Resources\Json\JsonResource as Resource;

class PaymentGatewayResource extends Resource
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
            'gateway' => $this->gateway ?? "",
            'gateway_id' => $this->gateway_id ?? "",
            'gateway_secret' => $this->gateway_secret ?? "",
            'is_enable' => isset($this->is_enable) ? ($this->is_enable ? "yes" : "no") : "",
            'agency' => isset($this->agency) ? new AgencyResource($this->whenLoaded('agency')) : "",
            'created_at' =>  $this->created_at ?? "",
        ];
    }
}
