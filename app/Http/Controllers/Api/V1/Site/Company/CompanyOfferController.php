<?php

namespace App\Http\Controllers\Api\V1\Site\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Offers\ChangeStatusRequest;
use App\Http\Requests\Api\V1\Offers\OfferRequest;
use App\Http\Resources\Api\V1\Offers\OfferCollection;
use App\Http\Resources\Api\V1\Offers\OfferResource;
use App\Models\V1\Offer;
use App\Services\Api\V1\Offers\OfferService;
use App\Traits\Translatable;
use App\Traits\UserTrait;

class CompanyOfferController extends Controller
{
    use UserTrait, Translatable;

    public function __construct(
        protected OfferService $offerService,
    ) {
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
        $offer = $this->offerService->store($request);

        if (isset($offer['error'])) {
            unset($offer['error']);
            return $this->validation_errors($offer);
        }

        return $this->createdResponse(new OfferResource($offer));
    }

    public function changeOfferStatus(ChangeStatusRequest $request, Offer $offer)
    {
        $offer = $this->offerService->changeOfferStatus($request, $offer);

        if (isset($offer['error'])) {
            unset($offer['error']);
            return $this->notFoundResponse($this->translateErrorMessage('offer', 'not_exists'));
        }
        return $this->success(new OfferResource($offer), 'Offer Updated Successfully');
    }


    public function destroy(Offer $offer)
    {
        $offer = $this->offerService->destroy($offer);
        if ($offer) return $this->success(null, 'Offer Deleted Successfully');
        return $this->notFoundResponse('Offer Not Found');
    }
}
