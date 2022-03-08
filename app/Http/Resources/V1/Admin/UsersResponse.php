<?php

namespace App\Http\Resources\V1\Admin;

use App\Http\Resources\BaseResponse;
use Illuminate\Http\Resources\Json\Resource;

class UsersResponse extends BaseResponse
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->wrapped(['users' => UserResource::collection($this)]);
    }
}
