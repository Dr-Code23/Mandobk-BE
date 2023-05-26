<?php

namespace App\Http\Resources\Api\V1\Profile;

use App\Traits\RoleTrait;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class ProfileResource extends JsonResource
{
    use RoleTrait;

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $resource = [
            'full_name' => $this->full_name,
            'username' => $this->username,
            'phone' => $this->phone,
            'role' => $this->whenLoaded('role', $this->getRoleNameById($this->role_id)),
            'avatar' => asset('storage/users/'.($this->avatar ?? 'user.png')),
            'password_changed' => $this->password_changed,
        ];
        if (isset($this->token)) {
            $resource['token'] = $this->token;
        }

        return $resource;
    }
}
