<?php

namespace App\Http\Resources\Api\V1\Site\OfferOrder;

use App\Http\Resources\Api\V1\Product\ProductResource;
use App\Traits\Translatable;
use Illuminate\Http\Resources\Json\JsonResource;

class OfferOrderResource extends JsonResource
{
    use Translatable;

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
            'date_from' => $this->from,
            'date_to' => $this->to,
            'product' => new OfferOrderProductResource($this->whenLoaded('product', $this->product)),
            'user' => new OfferOrderUserResource($this->whenLoaded('user'))
        ];
    }
}
