<?php

namespace App\Http\Resources\Api\V1\PayMethod;

use App\Traits\Translatable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class PayMethodResource extends JsonResource
{
    use Translatable;

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     *
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        return [
            'id' => $this->id,
            'name' => $this->translateWord($this->name),
        ];
    }
}
