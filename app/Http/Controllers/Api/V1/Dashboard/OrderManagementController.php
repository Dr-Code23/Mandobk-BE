<?php

namespace App\Http\Controllers\Api\V1\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Dashboard\OrderManagement\OrderManagementCollection;
use App\Models\V1\OfferOrder;
use App\Traits\HttpResponse;
use App\Traits\translationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as FacadesValidator;

class OrderManagementController extends Controller
{
    use HttpResponse;
    use translationTrait;

    public function index(Request $request)
    {
        return $this->resourceResponse(new OrderManagementCollection(OfferOrder::join('users as want_offer_users', 'want_offer_users.id', 'offer_orders.want_offer_id')
            ->join('offers', 'offers.id', 'offer_orders.offer_id')
            ->join('products', 'products.id', 'offers.product_id')
            ->join('users as offers_users', 'offers_users.id', 'offers.user_id')
            ->where(function ($query) use ($request) {
                if ($request->has('status') && $request->input('status') == 'pending') { // Fetch Pending Offers
                    $query->where('status', '1');
                } else {
                    // it's Offers Logs
                    $query->where('status', '!=', '1');
                }
            })
            ->select([
                'offer_orders.id as id',
                'products.com_name as commercial_name',
                'products.pur_price as purchase_price',
                'offer_orders.qty as quantity',
                'offer_orders.status as status',
                'offer_orders.created_at as created_at',
                'offers.bonus as bonus',
                'offers_users.full_name as offer_from_name',
                'want_offer_users.full_name as offer_to_name',
            ])
            ->get()
        ));
    }

    public function acceptPendingOrders(Request $request, OfferOrder $order)
    {
        if ($order->status == '1') {
            $validator = FacadesValidator::make(
                $request->all(),
                [
                    'approve' => ['required', 'boolean'],
                ], [
                    'approve.requried' => $this->translateErrorMessage('approve', 'required'),
                    'approve.boolean' => $this->translateErrorMessage('approve', 'boolean'),
                ]
            );
            if ($validator->fails()) {
                return $this->validation_errors($validator->errors());
            }
            $order->status = $request->input('approve') == '0' ? '0' : '2';
            $order->update();

            return $this->success(null, 'Status Changed Successfully');
        }

        return $this->noContentResponse();
    }
}
