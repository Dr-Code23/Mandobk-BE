<?php

namespace App\Http\Resources\Api\V1\Users;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $resource = [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'username' => $this->username,
            'role' => $this->role,
            'phone' => $this->phone,
        ];
        if (isset($this->token)) $resource['token'] = $this->token;
        $resource['created_at'] = $this->created_at;
        return $resource;
    }
}
