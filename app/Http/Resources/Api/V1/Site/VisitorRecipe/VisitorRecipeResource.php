<?php

namespace App\Http\Resources\Api\V1\Site\VisitorRecipe;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class VisitorRecipeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        return [
            'id' => $this->id,
            'alias' => $this->alias,
            'random_number' => $this->random_number,
            'details' => $this->details,
        ];
    }
}
