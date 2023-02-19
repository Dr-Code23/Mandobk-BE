<?php

namespace App\Http\Resources\Api\V1\Offers;

use App\Http\Resources\Api\V1\Product\ProductCollection;
use App\Http\Resources\Api\V1\Product\ProductResource;
use App\Models\V1\PayMethod;
use App\Traits\Translatable;
use Illuminate\Http\Resources\Json\JsonResource;

class OfferResource extends JsonResource
{
    use Translatable;

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
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
        ];
    }
}
