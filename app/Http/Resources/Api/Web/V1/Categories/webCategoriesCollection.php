<?php

namespace App\Http\Resources\Api\Web\V1\Categories;

use App\Traits\HttpResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

class webCategoriesCollection extends ResourceCollection
{
    use HttpResponse;

    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->resourceResponse(parent::toArray($request));
    }
}
