<?php

namespace App\Http\Resources\Api\V1\Dashboard\OrderManagement;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderManagementResource extends JsonResource
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
        $status = $this->status;

        return [
            'id' => $this->id,
            'commercial_name' => $this->commercial_name,
            'offer_from_name' => $this->offer_from_name,
            'offer_to_name' => $this->offer_to_name,
            'purchase_price' => $this->purchase_price,
            'quantity' => $this->quantity,
            'status' => $status == '0' ? 'Rejected' : ($status == '1' ? 'Pending' : 'Approved'),
            'bonus' => $this->bonus.'%',
            'created_at' => $this->created_at,
        ];
    }
}
