<?php

namespace App\Http\Controllers\Api\V1\Site\Offers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Offers\ChangeStatusRequest;
use App\Http\Requests\Api\V1\Offers\OfferRequest;
use App\Http\Resources\Api\V1\Offers\OfferCollection;
use App\Http\Resources\Api\V1\Offers\OfferResource;
use App\Models\V1\Offer;
use App\Services\Api\V1\Offers\OfferService;
use App\Traits\HttpResponse;
use App\Traits\Translatable;
use App\Traits\UserTrait;
use Illuminate\Http\JsonResponse;

class OfferController extends Controller
{
    use UserTrait, Translatable, HttpResponse;

    public function __construct(
        protected OfferService $offerService,
    )
    {
    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return $this->resourceResponse(new OfferCollection($this->offerService->allOffers()));
    }

    /**
     * @param Offer $offer
     * @return JsonResponse
     */
    public function show(Offer $offer)
    {
        $offer = $this->offerService->show($offer);

        if ($offer != null){
            return $this->resourceResponse(new OfferResource($offer));
        }

        return $this->notFoundResponse($this->translateErrorMessage('offer', 'not_exists'));
    }

    /**
     * @param OfferRequest $request
     * @return JsonResponse
     */
    public function store(OfferRequest $request)
    {
        $offer = $this->offerService->store($request);

        if (isset($offer['error'])) {
            unset($offer['error']);
            return $this->validation_errors($offer);
        }

        return $this->createdResponse(new OfferResource($offer));
    }

    /**
     * @param ChangeStatusRequest $request
     * @param Offer $offer
     * @return JsonResponse
     */
    public function changeOfferStatus(ChangeStatusRequest $request, Offer $offer): JsonResponse
    {
        $offer = $this->offerService->changeOfferStatus($request, $offer);

        if (isset($offer['error'])) {
            unset($offer['error']);
            return $this->notFoundResponse($this->translateErrorMessage('offer', 'not_exists'));
        }
        return $this->success(new OfferResource($offer), 'Offer Updated Successfully');
    }

    /**
     * @param Offer $offer
     * @return JsonResponse
     */
    public function destroy(Offer $offer): JsonResponse
    {
        $offer = $this->offerService->destroy($offer);

        if ($offer) {
            return $this->success(null, 'Offer Deleted Successfully');
        }

        return $this->notFoundResponse('Offer Not Found');
    }
}
