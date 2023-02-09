<?php

namespace App\Http\Resources\Api\V1\Site\Recipes;

use App\Traits\roleTrait;
use Illuminate\Http\Resources\Json\JsonResource;

class RecipeResource extends JsonResource
{
    use roleTrait;

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
            $resource['details'] = $this->details;
            $resource['updated_at'] = $this->updated_at;
        } else if ($this->roleNameIn(['pharmacy', 'pharmacy_sub_user'])) {
            $resource['visitor_name'] = $this->alias;
            $resource['doctor_name'] = $this->doctor_name;
        } else if ($this->getRoleNameForAuthenticatedUser() == 'doctor') {
            $resource['visitor_name'] = $this->alias;
        }
        $resource['created_at'] = $this->created_at;
        return $resource;
    }
}
