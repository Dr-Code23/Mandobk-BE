<?php

namespace App\Http\Resources\Api\V1\Site\OfferOrderManagement;

use Illuminate\Http\Resources\Json\ResourceCollection;

class OfferOrderManagementCollection extends ResourceCollection
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
