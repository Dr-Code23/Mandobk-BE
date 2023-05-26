<?php

namespace App\Http\Resources\Api\V1\Site\Pharmacy\SubUser;

use Illuminate\Http\Resources\Json\ResourceCollection;

class SubUserCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return parent::toArray($request);
    }
}
