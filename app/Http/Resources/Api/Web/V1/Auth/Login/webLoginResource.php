<?php

namespace App\Http\Resources\Api\Web\V1\Auth\Login;

use App\Traits\HttpResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class webLoginResource extends JsonResource
{
    use HttpResponse;

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->resourceResponse(parent::toArray($request));

        // return parent::toArray($request);
    }
}
