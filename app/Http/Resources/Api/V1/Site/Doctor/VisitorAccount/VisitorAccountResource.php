<?php

namespace App\Http\Resources\Api\V1\Site\Doctor\VisitorAccount;

use Illuminate\Http\Resources\Json\JsonResource;

class VisitorAccountResource extends JsonResource
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
            'name' => $this->name,
            'username' => $this->username,
            'phone' => $this->phone,
            'random_number_alias' => $this->alias,
            'random_number' => $this->random_number,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
