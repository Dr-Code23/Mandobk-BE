<?php

namespace App\Http\Resources\Api\V1\Offers;

use Illuminate\Http\Resources\Json\JsonResource;

class OfferProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'commercial_name' => $this->com_name,
            'scientific_name' => $this->sc_name,
            'concentrate' => $this->con,
            'bonus' => $this->bonus . '%',
            'selling_price' => $this->sel_price
        ];
    }
}
