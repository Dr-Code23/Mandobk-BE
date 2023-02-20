<?php

namespace App\Http\Resources\Api\V1\Product\ProductDetails;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
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
