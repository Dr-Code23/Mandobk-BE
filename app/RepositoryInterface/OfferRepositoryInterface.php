<?php

namespace App\RepositoryInterface;

interface OfferRepositoryInterface
{
    public function allOffers($request);

    public function showOneOffer($offer);

    public function storeOffer($request);

    public function updateOffer($request, $offer);

    public function destroyOffer($offer);

    public function getAllOffersForOthers();

    public function getAllOfferDurations();
}
