<?php

namespace App\Http\Resources\Api\V1\Translation;

use App\Traits\HttpResponse;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class translationResource extends JsonResource
{
    use HttpResponse;

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     *
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        return $this->resourceResponse(parent::toArray($request));
    }
}
