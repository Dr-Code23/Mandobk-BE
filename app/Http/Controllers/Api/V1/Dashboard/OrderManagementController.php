<?php

namespace App\Http\Controllers\Api\V1\Dashboard;

use App\Events\CustomerStatusEvent;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Dashboard\OrderManagement\OrderManagementCollection;
use App\Http\Resources\Api\V1\Dashboard\OrderManagement\OrderManagementResource;
use App\Models\V1\OfferOrder;
use App\Traits\HttpResponse;
use App\Traits\Translatable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OrderManagementController extends Controller
{
    use HttpResponse, Translatable;

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        return $this->resourceResponse(new OrderManagementCollection(
            OfferOrder::join(
                'users as want_offer_users',
                'want_offer_users.id',
                'offer_orders.want_offer_id'
            )
                ->join('offers', 'offers.id', 'offer_orders.offer_id')
                ->join('products', 'products.id', 'offers.product_id')
                ->join('users as offers_users', 'offers_users.id', 'offers.user_id')
                ->where(function ($query) use ($request) {
                    if ($request->has('status') && $request->input('status') == 'pending') { // Fetch Pending Offers
                        $query->where('offer_orders.status', '1');
                    } else {
                        // Fetch Offer orders logs
                        $query->where('offer_orders.status', '!=', '1');
                    }
                })
                ->select([
                    'offer_orders.id as id',
                    'products.com_name as commercial_name',
                    'products.pur_price as purchase_price',
                    'offer_orders.qty as quantity',
                    'offer_orders.status as status',
                    'offer_orders.created_at as created_at',
                    'offers_users.full_name as offer_from_name',
                    'want_offer_users.full_name as offer_to_name',
                ])
                ->get()
        ));
    }

    /**
     * @param Request $request
     * @param OfferOrder $order
     * @return \Illuminate\Http\JsonResponse|Response
     */
    public function acceptPendingOrders(Request $request, OfferOrder $order): Response|\Illuminate\Http\JsonResponse
    {
        if ($order->status == '1') {
            $order->status = $request->input('approve') ? '2' : '0';
            $order->update();

            $order = OfferOrder::where('offer_orders.id', $order->id)
                ->join(
                    'users as want_offer_users',
                    'want_offer_users.id',
                    'offer_orders.want_offer_id'
                )
                ->join('offers', 'offers.id', 'offer_orders.offer_id')
                ->join('products', 'products.id', 'offers.product_id')
                ->join('users as offers_users', 'offers_users.id', 'offers.user_id')
                ->select([
                    'offer_orders.id as id',
                    'products.com_name as commercial_name',
                    'products.pur_price as purchase_price',
                    'offer_orders.qty as quantity',
                    'offer_orders.status as status',
                    'offer_orders.created_at as created_at',
                    'offers.from as from_date',
                    'offers.to as to_date',
                    'offers_users.full_name as offer_from_name',
                    'want_offer_users.full_name as offer_to_name',
                    'want_offer_users.id as want_offer_id'
                ])->first();
            CustomerStatusEvent::dispatch($order, $order->want_offer_id);

            return $this->success(new OrderManagementResource($order), 'Status Changed Successfully');
        }

        return $this->noContentResponse();
    }
}
