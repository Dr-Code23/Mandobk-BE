<?php

namespace App\Http\Resources\Api\V1\Offers;

use App\Models\V1\PayMethod;
use App\Traits\translationTrait;
use Illuminate\Http\Resources\Json\JsonResource;

class OfferResource extends JsonResource
{
    use translationTrait;

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
            'scientefic_name' => $this->scientefic_name,
            'commercial_name' => $this->commercial_name,
            'product_id' => $this->product_id,
            'expire_date' => $this->expire_date,
            'offer_duration' => $this->translateWord($duration == '0' ? 'day' : ($duration == '1' ? 'week' : 'cheek')),
            'pay_method' => $this->translateWord(PayMethod::where('id', $this->pay_method)->first(['name'])->name),
            'works' => date('Y-m-d') <= $this->works_untill ? true : false,
            'works_untill' => $this->works_untill,
            'bonus' => $this->bonus,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
