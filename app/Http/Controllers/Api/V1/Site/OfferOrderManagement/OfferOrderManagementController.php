<?php

namespace App\Http\Controllers\Api\V1\Site\OfferOrderManagement;

use App\Http\Controllers\Controller;
use App\Models\V1\OfferOrder;
use App\Traits\HttpResponse;
use App\Traits\UserTrait;
use Illuminate\Http\Request;

class OfferOrderManagementController extends Controller
{
    use HttpResponse;
    use UserTrait;
    public function index()
    {
        return OfferOrder::whereIn(
            'offer_orders.want_offer_id',
            $this->getSubUsersForAuthenticatedUser()
        )
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
            ])
            ->get();

        // return $this->resourceResponse()
    }
}
