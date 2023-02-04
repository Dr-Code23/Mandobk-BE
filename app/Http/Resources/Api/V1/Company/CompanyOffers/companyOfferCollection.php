<?php

namespace App\Http\Resources\Api\V1\Company\CompanyOffers;

use Illuminate\Http\Resources\Json\ResourceCollection;

class companyOfferCollection extends ResourceCollection
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
