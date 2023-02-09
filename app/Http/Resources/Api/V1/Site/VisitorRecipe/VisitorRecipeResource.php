<?php

namespace App\Http\Resources\Api\V1\Site\VisitorRecipe;

use Illuminate\Http\Resources\Json\JsonResource;

class VisitorRecipeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
