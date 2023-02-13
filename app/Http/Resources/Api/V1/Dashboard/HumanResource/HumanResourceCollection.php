<?php

namespace App\Http\Resources\Api\V1\Dashboard\HumanResource;

use Illuminate\Http\Resources\Json\ResourceCollection;

class HumanResourceCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
