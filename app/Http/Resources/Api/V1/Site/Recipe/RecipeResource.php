<?php

namespace App\Http\Resources\Api\V1\Site\Recipe;

use App\Traits\RoleTrait;
use Illuminate\Http\Resources\Json\JsonResource;

class RecipeResource extends JsonResource
{
    use RoleTrait;

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
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
