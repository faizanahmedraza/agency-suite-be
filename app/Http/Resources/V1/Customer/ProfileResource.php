<?php

namespace App\Http\Resources\V1\Customer;

use Illuminate\Http\Resources\Json\JsonResource as Resource;

class ProfileResource extends Resource
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
            'first_name' => $this->first_name ?? '',
            'last_name' =>  $this->last_name ?? '',
            'image' =>$this->image ?? '',
        ];
    }
}
