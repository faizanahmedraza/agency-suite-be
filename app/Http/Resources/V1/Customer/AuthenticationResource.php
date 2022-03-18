<?php

namespace App\Http\Resources\V1\Customer;

use Illuminate\Http\Resources\Json\JsonResource as Resource;

class AuthenticationResource extends Resource
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
            'access_token' => $this['access_token'],
            'token_type' => $this['token_type'],
            'expiry' => $this['expires_at'],
            'agency' => new AgencyResource($this['agency']),
            'user' => new UserResource($this['user']),
        ];
    }
}
