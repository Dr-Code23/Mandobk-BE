<?php

namespace App\Http\Resources\Api\V1\Site\OfferOrder;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class OfferOrderProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        return [
            'commercial_name' => $this->com_name,
            'scientific_name' => $this->sc_name,
            'concentrate' => $this->con,
            'total_quantity' => $this->product_details_sum_qty,
        ];
    }
}
