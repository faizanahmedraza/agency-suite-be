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
            'default_domain' => $this->defaultDomain() ?? "",
            'other_domain' => $this->otherDomain() ?? "",
            'created_at' => $this->created_at,
        ];
    }
}
