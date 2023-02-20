<?php

namespace App\Http\Resources\Api\V1\Profile;

use App\Models\V1\Role;
use App\Traits\RoleTrait;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    use RoleTrait;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'full_name' => $this->full_name,
            'username' => $this->username,
            'phone' => $this->phone,
            'role' => $this->getRoleNameForAuthenticatedUser(),
            'avatar' => asset('storage/users/' . ($this->avatar ?? 'user.png')),
        ];
    }
}
