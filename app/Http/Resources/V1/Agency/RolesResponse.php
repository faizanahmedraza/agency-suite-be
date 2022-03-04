<?php

namespace App\Http\Resources\V1\Agency;

use App\Http\Resources\BaseResponse;
use Illuminate\Http\Resources\Json\Resource;

class RolesResponse extends BaseResponse
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->wrapped(['roles' => (!$this->isEmpty()) ? RoleResource::collection($this) : []]);
    }
}
