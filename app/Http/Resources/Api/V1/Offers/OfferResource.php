<?php

namespace App\Http\Resources\Api\V1\Offers;

use App\Traits\Translatable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class OfferResource extends JsonResource
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
        $duration = $this->offer_duration;

        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'start_date' => $this->from,
            'end_date' => $this->to,
            'pay_method_id' => $this->pay_method,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'product_info' => new OfferProductResource($this->whenLoaded('product')),
        ];
    }
}
