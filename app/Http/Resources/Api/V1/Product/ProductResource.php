<?php

namespace App\Http\Resources\Api\V1\Product;

use App\Http\Resources\Api\V1\Users\UserCollection;
use App\Http\Resources\Api\V1\Users\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $resource = [
            'id' => $this->id,
            'commercial_name' => $this->com_name,
            'scientific_name' => $this->sc_name,
            'purchase_price' => $this->pur_price,
            'selling_price' => $this->sel_price,
            'bonus' => $this->bonus,
            'concentrate' => $this->con,
            'barcode' => asset('/storage/products/' . $this->barcode) . '.svg',
            'product_details' => $this->whenLoaded('product_details'),
        ];
        if ($request->is('data_entry/*') || $request->is('data_entry/')) {
            $resource['limited'] = $this->limited ? true : false;
        }
        if (isset($this->detail))
            $resource['detail'] = $this->detail;
        return $resource;
    }
}
