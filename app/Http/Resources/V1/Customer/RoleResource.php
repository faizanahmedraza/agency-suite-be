<?php

namespace App\Http\Resources\V1\Customer;

use App\Models\Role;
use Illuminate\Http\Resources\Json\JsonResource as Resource;

class RoleResource extends Resource
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
            'name' => removePrefix($this->name,Role::ROLES_PREFIXES['agency']),
            'created_at' => $this->created_at,
            'permissions' =>  PermissionResource::collection($this->whenLoaded('permissions'))
        ];
    }
}
