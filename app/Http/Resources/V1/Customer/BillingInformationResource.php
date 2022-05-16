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
            'invoice_to' => $this->invoice_to ?? '',
            'address' => $this->address ?? '',
            'country' => $this->country ?? '',
            'city' => $this->city ?? '',
            'state' => $this->state ?? '',
            'zip_code' => $this->zip_code ?? '',
            'tax_code' => $this->tax_code ?? '',
            'created_at' => $this->created_at ?? '',
        ];
    }
}
