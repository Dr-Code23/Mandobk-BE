<?php

namespace App\Http\Resources\Api\V1\Dashboard\OrderManagement;

use App\Traits\Translatable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class OrderManagementResource extends JsonResource
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
        $status = $this->status;

        return [
            'id' => $this->id,
            'commercial_name' => $this->commercial_name,
            'offer_from_name' => $this->offer_from_name,
            'offer_to_name' => $this->offer_to_name,
            'purchase_price' => $this->purchase_price,
            'quantity' => $this->quantity,
            'status' => $status == '0' ? $this->translateWord('rejected') : ($status == '1' ? 'Pending' : $this->translateWord('approved')),
            'status_code' => $status == '0' ? '0' : '1',
            // 'bonus' => $this->bonus . ' %',
            'created_at' => $this->created_at,
        ];
    }
}
