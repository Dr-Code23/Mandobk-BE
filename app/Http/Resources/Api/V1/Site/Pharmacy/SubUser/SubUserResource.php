<?php

namespace App\Http\Resources\Api\V1\Site\Pharmacy\SubUser;

use Illuminate\Http\Resources\Json\JsonResource;

class SubUserResource extends JsonResource
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
            'name' => $this->full_name,
            'username' => $this->username,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
