<?php

namespace App\Http\Resources\Api\V1\Users;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $resource = [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'username' => $this->username,
            'role' => $this->role,
            'status' => $this->status,
            'phone' => $this->phone,
        ];
        if (isset($this->token)) {
            $resource['token'] = $this->token;
        }
        $resource['created_at'] = $this->created_at;

        return $resource;
    }
}
