<?php

namespace App\Http\Resources\Api\Web\V1\Company\CompanyOffers;

use App\Models\Api\Web\V1\PayMethod;
use App\Traits\translationTrait;
use Illuminate\Http\Resources\Json\JsonResource;

class companyOfferResource extends JsonResource
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
            'scientefic_name' => $this->sc_name,
            'commercial_name' => $this->com_name,
            'offer_duration_id' => $this->offer_duration,
            'offer_duration_name' => $this->translateWord('Company/CompanyOffers/companyOffersTranslationFile.offer_durations.'.($duration == '0' ? 'day' : ($duration == '1' ? 'week' : 'cheek'))),
            'pay_method' => PayMethod::where('id', $this->pay_method)->first(['name'])->name,
            'bonus' => $this->bonus,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
