<?php

namespace App\RepositoryInterface;

interface CompanyOffersRepositoryInterface
{
    public function allCompanyOffers();

    public function showOneCompanyOffer($offer);

    public function storeCompanyOffer($request);

    public function updateCompanyOffer($request, $offer);

    public function destroyCompanyOffer($offer);
}
