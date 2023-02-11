<?php

namespace App\Http\Controllers\Api\V1\Site\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Offers\OffersRequest;
use App\Models\V1\Offer;
use App\RepositoryInterface\OfferRepositoryInterface;
use App\Traits\userTrait;
use Illuminate\Http\Request as HttpRequest;

class CompanyOffersController extends Controller
{
    use userTrait;
    private $companyOffers;

    public function __construct(OfferRepositoryInterface $companyOffers)
    {
        $this->companyOffers = $companyOffers;
    }

    public function index(HttpRequest $request)
    {
        return $this->companyOffers->allOffers($request);
    }

    public function show(Offer $offer)
    {
        return $this->companyOffers->showOneOffer($offer);
    }

    public function store(OffersRequest $request)
    {
        return $this->companyOffers->storeOffer($request);
    }

    public function update(OffersRequest $request, Offer $offer)
    {
        return $this->companyOffers->updateOffer($request, $offer);
    }

    public function destroy(Offer $offer)
    {
        return $this->companyOffers->destroyOffer($offer);
    }

    public function offerDurations()
    {
        return $this->companyOffers->getAllOfferDurations();
    }
}
