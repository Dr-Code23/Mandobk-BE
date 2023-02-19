<?php

namespace App\Http\Controllers\Api\V1\Site\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Offers\OfferRequest;
use App\Http\Resources\Api\V1\Offers\OfferCollection;
use App\Http\Resources\Api\V1\Offers\OfferResource;
use App\Models\V1\Offer;
use App\RepositoryInterface\OfferRepositoryInterface;
use App\Services\Api\V1\Offers\OfferService;
use App\Traits\Translatable;
use App\Traits\UserTrait;
use Illuminate\Http\Request as HttpRequest;

class CompanyOfferController extends Controller
{
    use UserTrait;
    use Translatable;
    private $companyOffers;

    public function __construct(
        OfferRepositoryInterface $companyOffers,
        protected OfferService $offerService,
    ) {
        $this->companyOffers = $companyOffers;
    }

    public function index()
    {
        return $this->resourceResponse(new OfferCollection($this->offerService->allOffers()));
    }

    public function show(Offer $offer)
    {
        $offer = $this->offerService->show($offer);
        if ($offer != null)

            return $this->resourceResponse(new OfferResource($offer));

        return $this->notFoundResponse($this->translateErrorMessage('offer', 'not_exists'));
    }

    public function store(OfferRequest $request)
    {
        return $this->companyOffers->storeOffer($request);
    }

    public function update(OfferRequest $request, Offer $offer)
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
