<?php

namespace App\Http\Resources\Api\V1\Site\OfferOrder;

use App\Traits\TranslationTrait;
use Illuminate\Http\Resources\Json\JsonResource;

class OfferOrderResource extends JsonResource
{
    use TranslationTrait;

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
            'scientific_name' => $this->sc_name,
            'bonus' => $this->bonus,
            'expire_date' => $this->expire_date,
            'con' => $this->con,
            'selling_price' => $this->sel_price,
            'duration' => $this->translateWord($this->duration == '0' ? 'day' : ($this->duration == '1' ? 'week' : 'cheek')),
        ];
    }
}
