<?php

namespace App\Http\Resources\Api\V1\Product;

use App\Http\Resources\Api\V1\Product\ProductDetails\ProductDetailsCollection;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {
        $resource = [
            'id' => $this->id,
            'commercial_name' => $this->com_name,
            'scientific_name' => $this->sc_name,
            'purchase_price' => $this->pur_price,
            'selling_price' => $this->sel_price,
            'bonus' => $this->bonus.'%',
            'concentrate' => $this->con.'%',
            'limited' => (bool) $this->limited,
            'limited_changed' => $this->whenHas('limited_changed'),
            'total_quantity' => $this->whenHas('product_details_sum_qty', $this->product_details_sum_qty),
            'detail' => $this->whenHas('detail', $this->detail),
            'barcode' => $this->barcode ? asset('/storage/products/'.$this->barcode).'.svg' : null,
            'product_details' => new ProductDetailsCollection($this->whenLoaded('product_details')),
        ];
        if ($request->is('ceo/*') || $request->is('ceo/') || $request->is('data_entry/*') || $request->is('data_entry/')) {
            $resource['limited'] = (bool) $this->limited;
        }

        return $resource;
    }
}
