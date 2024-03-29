<?php

namespace App\Http\Resources\Api\V1\Dashboard\DataEntry;

use App\Traits\DateTrait;
use Illuminate\Http\Resources\Json\JsonResource;

class dataEntryResource extends JsonResource
{
    use DateTrait;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'commercial_name' => $this->com_name,
            'scientific_name' => $this->sc_name,
            'quantity' => $this->qty,
            'purchase_price' => $this->pur_price,
            'selling_price' => $this->sel_price,
            'bonus' => $this->bonus,
            'concentrate' => $this->con,
            'limited' => (bool) $this->limited,
            'patch_number' => $this->patch_number,
            'provider' => $this->provider,
            'barcode' => asset('storage/data_entry/'.$this->barcode).'.svg',
            'entry_date' => $this->changeDateFormat($this->entry_date),
            'expire_date' => $this->changeDateFormat($this->expire_date),
            'created_at' => $this->changeDateFormat($this->created_at),
            'updated_at' => $this->changeDateFormat($this->updated_at),
        ];
    }
}
