<?php

namespace App\Http\Resources\Api\V1\Site\Recipe;

use App\Traits\RoleTrait;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class RecipeResource extends JsonResource
{
    use RoleTrait;

    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $resource = [];
        if ($this->getRoleNameForAuthenticatedUser() == 'visitor') {
            $resource['alias'] = $this->alias;
            $resource['random_number'] = $this->random_number;
            $resource['products'] = $this->details['products'] ?? [];
            $resource['doctor_name'] = $this->details['doctor_name'] ?? null;
            $resource['updated_at'] = $this->updated_at;
        } elseif ($this->roleNameIn(['pharmacy', 'pharmacy_sub_user'])) {
            $resource['visitor_name'] = $this->alias;
            $resource['doctor_name'] = $this->doctor_name;
        } elseif ($this->getRoleNameForAuthenticatedUser() == 'doctor') {
            $resource['visitor_name'] = $this->alias;
        }
        $resource['created_at'] = $this->created_at;

        return $resource;
    }
}
