<?php

namespace App\Http\Resources\Api\V1\Product;

use Illuminate\Http\Resources\Json\JsonResource;

class productResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $resource = [
            'id' => $this->id,
            'commercial_name' => $this->com_name,
            'scientific_name' => $this->sc_name,
            'quantity' => $this->qty,
            'purchase_price' => $this->pur_price,
            'selling_price' => $this->sel_price,
            'bonus' => $this->bonus,
            'concentrate' => $this->con,
            'patch_number' => $this->patch_number,
            'provider' => $this->provider,
            'bar_code' => asset('storage/products/'.$this->bar_code).'.svg',
            'entry_date' => $this->entry_date,
            'expire_date' => $this->expire_date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
        if ($request->is('data_entry/*') || $request->is('data_entry/')) {
            $resource['limited'] = $this->limited ? true : false;
        }

        return $resource;
    }
}
