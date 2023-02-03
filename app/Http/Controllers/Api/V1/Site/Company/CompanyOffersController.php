<?php

namespace App\Http\Controllers\Api\V1\Site\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Web\V1\Company\CompanyOffers\companyOfferRequest;
use App\Models\Api\Web\V1\CompanyOffer;
use App\RepositoryInterface\CompanyOffersRepositoryInterface;

class CompanyOffersController extends Controller
{
    private $companyOffers;

    public function __construct(CompanyOffersRepositoryInterface $companyOffers)
    {
        $this->companyOffers = $companyOffers;
    }

    public function index()
    {
        return $this->companyOffers->allCompanyOffers();
    }

    public function show(CompanyOffer $offer)
    {
        return $this->companyOffers->showOneCompanyOffer($offer);
    }

    public function store(companyOfferRequest $request)
    {
        return $this->companyOffers->storeCompanyOffer($request);
    }

    public function update(companyOfferRequest $request, CompanyOffer $offer)
    {
        return $this->companyOffers->updateCompanyOffer($request, $offer);
    }

    public function destroy(CompanyOffer $offer)
    {
        return $this->companyOffers->destroyCompanyOffer($offer);
    }
}
