<?php

namespace App\Http\Resources\V1\Agency;

use Illuminate\Http\Resources\Json\JsonResource as Resource;

class InvoiceItemResource extends Resource
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
            'id' => $this->id ?? "",
            'name' => $this->name ?? "",
            'rate' => $this->rate ?? "",
            'quantity' =>  $this->quantity ?? "",
            'discount' =>  $this->discount ?? "",
            'gross_amount' =>  $this->gross_amount ?? "",
            'net_amount' =>  $this->net_amount ?? "",
            'created_at' =>  $this->created_at ?? "",
        ];
    }
}
