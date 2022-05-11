<?php

namespace App\Http\Resources\V1\Customer;

use App\Http\Resources\BaseResponse;

class ServiceResponse extends BaseResponse
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->wrapped([
            'service' => new ServiceResource($this)
        ]);
    }
}
