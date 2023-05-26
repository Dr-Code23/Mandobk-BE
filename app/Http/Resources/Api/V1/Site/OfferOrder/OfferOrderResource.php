<?php

namespace App\Http\Resources\Api\V1\Site\OfferOrder;

use App\Traits\Translatable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class OfferOrderResource extends JsonResource
{
    use Translatable;

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        return [
            'id' => $this->id,
            'date_from' => $this->from,
            'date_to' => $this->to,
            'product' => new OfferOrderProductResource($this->whenLoaded('product', $this->product)),
            'user' => new OfferOrderUserResource($this->whenLoaded('user')),
        ];
    }
}
