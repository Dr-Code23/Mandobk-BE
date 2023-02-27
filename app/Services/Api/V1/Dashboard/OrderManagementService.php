<?php

namespace App\Services\Api\V1\Dashboard;

use App\Models\V1\OfferOrder;

class OrderManagementService
{
    public function index($request)
    {
        return OfferOrder::join(
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
            ->get(
                [
                    'offer_orders.id as id',
                    'products.com_name as commercial_name',
                    'products.pur_price as purchase_price',
                    'offer_orders.qty as quantity',
                    'offer_orders.status as status',
                    'offer_orders.created_at as created_at',
                    'offers_users.full_name as offer_from_name',
                    'want_offer_users.full_name as offer_to_name',
                ]
            );
    }
}
