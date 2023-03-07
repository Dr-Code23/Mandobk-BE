<?php

namespace App\Http\Resources\Api\V1\Archive;

use Illuminate\Http\Resources\Json\JsonResource;

class ArchiveResource extends JsonResource
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
            'id' => $this->id,
            'alias' => $this->alias,
            'random_number' => $this->random_number,
            'products' => $this->details['products'],
            'doctor_name' => $this->details['doctor_name'],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
