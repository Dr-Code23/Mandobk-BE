<?php

namespace App\Services\Api\V1\Offers;

use App\Models\V1\Offer;
use App\Models\V1\Product;
use App\Models\V1\Role;
use App\Traits\RoleTrait;
use App\Traits\UserTrait;
use Auth;
use Illuminate\Database\Eloquent\Collection;

class OfferService
{
    use UserTrait;
    use RoleTrait;
    public function __construct(
        protected Offer $offerModel,
        protected Role $roleModel,
        protected Product $productModel,
    ) {
    }

    /**
     * Fetch All Offers
     *
     * @return Collection
     */
    public function allOffers(): Collection
    {

        return Offer::where('user_id', Auth::id())
            ->where('type', '1')
            ->get();
    }

    /**
     * Show One Offer
     *
     * @param Offer $offer
     * @return Offer|null
     */
    public function show($offer): Offer|null
    {
        if ($offer->user_id == Auth::id()) return $offer;
        else return null;
    }
}
