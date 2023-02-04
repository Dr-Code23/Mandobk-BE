<?php

namespace App\Http\Resources\Api\V1\Roles;

use App\Traits\translationTrait;
use Illuminate\Http\Resources\Json\JsonResource;

class rolesResource extends JsonResource
{
    use translationTrait;

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
            'name' => $this->translateWord($this->name),
        ];
    }
}
