<?php

namespace App\Http\Controllers\Api\V1\Site\OfferOrder;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Site\OfferOrder\OfferOrderRequest;
use App\Http\Resources\Api\V1\Site\OfferOrder\OfferOrderCollection;
use App\Services\Api\V1\Site\OfferOrder\OfferOrderService;
use App\Traits\HttpResponse;
use App\Traits\Translatable;
use App\Traits\UserTrait;
use Illuminate\Http\JsonResponse;

class OfferOrderController extends Controller
{
    use HttpResponse, Translatable, UserTrait;

    public function __construct(
        private readonly OfferOrderService $offerOrderService
    ) {
    }

        /**
         * Show All Offers
         */
        public function showAllOffers(): JsonResponse
        {
            return $this->resourceResponse(
                new OfferOrderCollection($this->offerOrderService->showAllOffers())
            );
        }

        /**
         * Make Order
         */
        public function order(OfferOrderRequest $request): JsonResponse
        {
            $order = $this->offerOrderService->order($request);

            if (is_string($order) && $order) {
                return $this->success(null, $order);
            } elseif (isset($order['offer_not_found'])) {
                return $this->notFoundResponse(
                    $this->translateErrorMessage('offer', 'not_found')
                );
            }

            return $this->validationErrorsResponse($order);
        }
}
