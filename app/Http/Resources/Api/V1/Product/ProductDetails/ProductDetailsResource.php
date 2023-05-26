<?php

namespace App\Http\Resources\Api\V1\Product\ProductDetails;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class ProductDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'quantity' => $this->qty,
            'expire_date' => $this->expire_date,
            'patch_number' => $this->patch_number,
            'created_at' => $this->created_at,
        ];
    }
}
