<?php

namespace App\Http\Controllers\Api\V1\Site\OfferOrder;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Site\OfferOrder\OfferOrderRequest;
use App\Http\Resources\Api\V1\Site\OfferOrder\OfferOrderCollection;
use App\Models\Api\V1\Offer;
use App\Models\Api\V1\OfferOrder;
use App\Traits\HttpResponse;
use App\Traits\translationTrait;
use App\Traits\userTrait;
use Illuminate\Http\Request;

class OfferOrderController extends Controller
{
    use HttpResponse;
    use translationTrait;
    use userTrait;

    public function index(Request $request)
    {
        $type = $request->routeIs('order-company-show') ? '1' : '2';

        $offers = Offer::join('products', 'products.id', 'offers.product_id')
            ->where('offers.type', $type)
            ->where(function ($query) use ($request) {
                $duration = $request->input('type');
                if ($duration == 'day') {
                    $query->where('offers.offer_duration', '0');
                } elseif ($duration == 'week') {
                    $query->where('offers.offer_duration', '1');
                } elseif ($duration == 'cheek') {
                    $query->where('offers.offer_duration', '2');
                }
            })
            ->where('offers.works_untill', '>=', date('Y-m-d'))
            ->get([
                'offers.id as id',
                'products.com_name as com_name',
                'products.sc_name as sc_name',
                'offers.bonus as bonus',
                'products.expire_date as expire_date',
                'products.con as con',
                'products.sel_price as sel_price',
                'offers.offer_duration as duration',
            ]);

        return $this->resourceResponse(new OfferOrderCollection($offers));
    }

    public function order(OfferOrderRequest $request)
    {
        // Check if the offer Belong to a company and is not expired
        $offer = Offer::join('products', 'products.id', 'offers.product_id')
            ->where('offers.id', $request->offer_id)
            ->where('works_untill', '>=', date('Y-m-d'))
            ->where('offers.type', '1')
            ->first([
                'products.qty',
            ]);

        if ($offer) {
            if ($offer->qty >= $request->quantity) {
                // Everything is valid so add the data
                OfferOrder::create([
                    'offer_id' => $request->offer_id,
                    'want_offer_id' => $this->getAuthenticatedUserId(),
                    'qty' => $request->quantity,
                ]);

                return $this->success(null, 'Order Made Successfully , waiting admin response');
            }

            return $this->validation_errors([
                'quantity' => $this->translateWord('quantity').' Cannot Be Greater than existing quantiy ('.$offer->qty.')',
            ]);
        }

        return $this->notFoundResponse();
    }
}
