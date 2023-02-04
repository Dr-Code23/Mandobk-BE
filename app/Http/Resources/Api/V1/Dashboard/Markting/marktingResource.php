<?php

namespace App\Http\Resources\Api\V1\Dashboard\Markting;

use Illuminate\Http\Resources\Json\JsonResource;

class marktingResource extends JsonResource
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
            'medicine_name' => $this->medicine_name,
            'company_name' => $this->company_name,
            'discount' => $this->discount,
            'img' => asset('storage/markting/'.$this->img),
        ];
    }
}
