<?php

namespace App\Http\Resources\Api\Web\V1\Categories;

use Illuminate\Http\Resources\Json\JsonResource;

class webCategoriesResource extends JsonResource
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
        return [
            'id' => $this->id,
            'commercial_name' => $this->com_name,
            'scientefic_name' => $this->sc_name,
            'quantity' => $this->qty,
            'purchase_price' => $this->pur_price,
            'selling_price' => $this->sel_price,
            'bonus' => $this->bonus,
            'concentrate' => $this->con,
            'patch_number' => $this->patch_number,
            'provider' => $this->provider,
            'bar_code' => asset('/storage/categories/'.$this->bar_code).'.svg',
            'entry_date' => date('d / m / Y', strtotime($this->created_at)),
            'expire_date' => date('d / m / Y', strtotime($this->expire_in)),
        ];
    }
}
