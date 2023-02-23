<?php

namespace App\Http\Resources\Api\V1\Site\OfferOrder;

use Illuminate\Http\Resources\Json\JsonResource;

class OfferOrderProductResource extends JsonResource
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
            'total_quantity' => $this->product_details_sum_qty,
        ];
    }
}
