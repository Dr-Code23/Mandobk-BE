<?php

namespace App\Http\Controllers\Api\V1\Site\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Company\CompanyOffers\companyOfferRequest;
use App\Models\Api\V1\Offer;
use App\RepositoryInterface\OfferRepositoryInterface;
use App\RepositoryInterface\ProductRepositoryInterface;

class CompanyOffersController extends Controller
{
    private $companyOffers;
    private $productRepository;

    public function __construct(OfferRepositoryInterface $companyOffers, ProductRepositoryInterface $productRepository)
    {
        $this->companyOffers = $companyOffers;
        $this->productRepository = $productRepository;
    }

    public function index()
    {
        return $this->companyOffers->allOffers();
    }

    public function show(Offer $offer)
    {
        return $this->companyOffers->showOneOffer($offer);
    }

    public function store(companyOfferRequest $request)
    {
        return $this->companyOffers->storeOffer($request);
    }

    public function update(companyOfferRequest $request, Offer $offer)
    {
        return $this->companyOffers->updateOffer($request, $offer);
    }

    public function destroy(Offer $offer)
    {
        return $this->companyOffers->destroyOffer($offer);
    }

    public function ScienteficNames()
    {
        return $this->productRepository->getAllScienteficNamesInSelect();
    }
}
