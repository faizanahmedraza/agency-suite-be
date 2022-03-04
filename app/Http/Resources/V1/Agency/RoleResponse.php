<?php

namespace App\Http\Resources\V1\Agency;

use App\Http\Resources\BaseResponse;

class RoleResponse extends BaseResponse
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->wrapped(['role' => new RoleResource($this)]);
    }
}
