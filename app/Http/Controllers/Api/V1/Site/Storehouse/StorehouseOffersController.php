<?php

namespace App\Http\Controllers\Api\V1\Site\Storehouse;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Offers\OffersRequest;
use App\Models\Api\V1\Offer;
use App\RepositoryInterface\OfferRepositoryInterface;
use App\Traits\userTrait;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;

class StorehouseOffersController extends Controller
{
    use userTrait;
    private $storehouseOffers;

    public function __construct(OfferRepositoryInterface $storehouseOffers)
    {
        $this->storehouseOffers = $storehouseOffers;
    }

    public function index(HttpFoundationRequest $request)
    {
        return $this->storehouseOffers->allOffers($request);
    }

    public function show(Offer $offer)
    {
        return $this->storehouseOffers->showOneOffer($offer);
    }

    public function store(OffersRequest $request)
    {
        return $this->storehouseOffers->storeOffer($request);
    }

    public function update(OffersRequest $request, Offer $offer)
    {
        return $this->storehouseOffers->updateOffer($request, $offer);
    }

    public function destroy(Offer $offer)
    {
        return $this->storehouseOffers->destroyOffer($offer);
    }
}
