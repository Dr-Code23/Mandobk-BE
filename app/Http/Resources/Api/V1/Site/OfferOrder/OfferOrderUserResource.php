<?php

namespace App\Http\Resources\Api\V1\Site\OfferOrder;

use Illuminate\Http\Resources\Json\JsonResource;

class OfferOrderUserResource extends JsonResource
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
            'full_name' => $this->full_name,
        ];
    }
}
