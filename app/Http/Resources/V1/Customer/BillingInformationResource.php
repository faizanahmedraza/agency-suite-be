<?php

namespace App\Http\Resources\V1\Customer;

use App\Models\Role;
use Illuminate\Http\Resources\Json\JsonResource as Resource;

class BillingInformationResource extends Resource
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
            'id' => $this->id ?? '',
            'holder_name' => $this->holder_name ?? '',
            'last_digits' => $this->last_digits ?? '',
            'exp_month' => $this->exp_month ?? '',
            'exp_year' => $this->exp_year ?? '',
            'address' => $this->address ?? '',
            'country' => $this->country ?? '',
            'city' => $this->city ?? '',
            'state' => $this->state ?? '',
            'street' => $this->street ?? '',
            'zip_code' => $this->zip_code ?? '',
            'created_at' => $this->created_at ?? '',
        ];
    }
}
