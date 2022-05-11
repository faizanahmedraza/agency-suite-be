<?php

namespace App\Http\Resources\V1\Customer;

use App\Models\Service;
use Illuminate\Http\Resources\Json\JsonResource as Resource;
use App\Models\User;

class ServiceIntakeResource extends Resource
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
            'intake' => json_decode($this->intake),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
