<?php

namespace App\Http\Resources\V1\Agency;

use App\Models\Role;
use Illuminate\Http\Resources\Json\JsonResource as Resource;

class PortalSettingResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            "primary_color" => $this->primary_color,
            "secondary_color" => $this->secondary_color,
            "logo" => $this->logo,
            "favicon" => $this->favicon,
            'created_at' => $this->created_at,
            'agency' => new AgencyResource($this->whenLoaded('agency')),
        ];
    }
}
