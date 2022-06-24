<?php

namespace App\Http\Resources\V1\Agency;

use App\Http\Resources\BaseResponse;

class PaymentGatewayResponse extends BaseResponse
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->wrapped(['payment_gateway' =>  new PaymentGatewayResource($this)]);
    }
}
