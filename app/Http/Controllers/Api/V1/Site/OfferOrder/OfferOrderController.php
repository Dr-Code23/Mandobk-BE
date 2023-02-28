<?php

namespace App\Http\Controllers\Api\V1\Site\OfferOrder;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Site\OfferOrder\OfferOrderRequest;
use App\Http\Resources\Api\V1\Site\OfferOrder\OfferOrderCollection;
use App\Models\V1\Offer;
use App\Models\V1\OfferOrder;
use App\Services\Api\V1\Site\OfferOrder\OfferOrderService;
use App\Traits\HttpResponse;
use App\Traits\Translatable;
use App\Traits\UserTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OfferOrderController extends Controller
{
    use HttpResponse;
    use Translatable;
    use UserTrait;

    public function __construct(
        private OfferOrderService $offerOrderService
    )
    {
    }

    /**
     * Show All Offers
     * @return JsonResponse
     */
    public function showAllOffers(): JsonResponse
    {
        return $this->resourceResponse(
            new OfferOrderCollection($this->offerOrderService->showAllOffers())
        );
    }

    /**
     * Make Order
     * @param OfferOrderRequest $request
     * @return JsonResponse
     */
    public function order(OfferOrderRequest $request): JsonResponse
    {
        $order = $this->offerOrderService->order($request);
        if(is_string($order) && $order){
            return $this->success(null, $order);
        }
        else if (isset($order['offer_not_found'])){
            return $this->notFoundResponse($this->translateErrorMessage('offer' , 'not_found'));
        }
        return $this->validation_errors($order);
    }
}
