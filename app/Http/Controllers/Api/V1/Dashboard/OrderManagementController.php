<?php

namespace App\Http\Controllers\Api\V1\Dashboard;

use App\Events\CustomerStatusEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Dashboard\OrderManagementRequest;
use App\Http\Resources\Api\V1\Dashboard\OrderManagement\OrderManagementCollection;
use App\Http\Resources\Api\V1\Dashboard\OrderManagement\OrderManagementResource;
use App\Models\V1\OfferOrder;
use App\Services\Api\V1\Dashboard\OrderManagementService;
use App\Traits\HttpResponse;
use App\Traits\Translatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderManagementController extends Controller
{
    use HttpResponse, Translatable;

    public function __construct(
        private OrderManagementService $orderManagementService
    ) {
    }

    /**
     * Show All Orders
     */
    public function index(Request $request): JsonResponse
    {
        return $this->resourceResponse(
            new OrderManagementCollection(
                $this->orderManagementService->index($request)
            )
        );
    }

    /**
     * Manage Pending Orders
     */
    public function managePendingOrders(OrderManagementRequest $request, OfferOrder $order): JsonResponse
    {
        $order = $this->orderManagementService->managePendingOrders($request, $order);

        if ($order instanceof OfferOrder) {
            CustomerStatusEvent::dispatch($order, $order->want_offer_id);

            return $this->success(
                new OrderManagementResource($order),
                $this->translateSuccessMessage(
                    'order',
                    'updated'
                )
            );
        }

        return $this->notFoundResponse(
            msg: $this->translateErrorMessage(
                'order',
                'not_found'
            )
        );
    }
}
