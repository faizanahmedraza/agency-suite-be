<?php

namespace App\Http\Resources\V1\Agency;

use Illuminate\Http\Resources\Json\JsonResource as Resource;

class AgencyResource extends Resource
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
            'default_domain' => $this->defaultDomain() ?  $this->defaultDomain()->domain : "",
            'custom_domain' => $this->customDomain() ? $this->customDomain()->domain : "",
            'created_at' => $this->created_at,
        ];
    }
}
