<?php

namespace App\Http\Resources\Api\V1\Dashboard\Markting;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class MarktingResource extends JsonResource
{
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
            'medicine_name' => $this->medicine_name,
            'company_name' => $this->company_name,
            'discount' => $this->discount . (
               $request->route('ad') && $request->method() == 'GET'
                    ? ''
                    : '%'
                ),
            'img' => asset('/storage/markting/' . $this->img),
        ];
    }
}
