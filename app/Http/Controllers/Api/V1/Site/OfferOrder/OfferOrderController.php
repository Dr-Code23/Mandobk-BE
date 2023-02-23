<?php

namespace App\Http\Controllers\Api\V1\Site\OfferOrder;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Site\OfferOrder\OfferOrderRequest;
use App\Http\Resources\Api\V1\Site\OfferOrder\OfferOrderCollection;
use App\Models\V1\Offer;
use App\Models\V1\OfferOrder;
use App\Traits\HttpResponse;
use App\Traits\Translatable;
use App\Traits\UserTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OfferOrderController extends Controller
{
    use HttpResponse;
    use Translatable;
    use UserTrait;

    public function index(Request $request)
    {
        $offers = Offer::with(['product', 'user'])
            ->where('type', auth()->user()->role->name == 'storehouse' ? '1' : '2')
            ->where('to', '>=', now())
            ->get();
        return $this->resourceResponse(new OfferOrderCollection($offers));
    }

    public function order(OfferOrderRequest $request)
    {

        $offer = Offer::with(['product' => function ($query) {
            $query->select('id');
            $query->withSum('product_details', 'qty');
        }])
            ->where('id', $request->offer_id)
            ->where('to', '>=', now())
            ->first();
        return $offer;
        if ($offer) {
            if ($offer->type == (auth()->user()->role->name == 'storehouse' ? '1'
                : (auth()->user()->role->name == 'pharmacy' ? '2' : null)
            )) {
            }
        }
        return $offer;
        // Check if the offer Belong to a company and is not expired
        $offer = Offer::join('products', 'products.id', 'offers.product_id')
            ->where('offers.id', $request->offer_id)
            ->where('works_untill', '>=', date('Y-m-d'))
            ->where('offers.type', $request->routeIs('order-company-make') ? '1' : '2')
            ->first([
                'products.qty',
                'offers.id as id',
            ]);
        if ($offer) {
            $qty = (int) $request->qty;
            $offer_order = OfferOrder::where('offer_id', $offer->id)
                ->where('status', '1')
                ->where('want_offer_id', Auth::id())
                ->first(['id']);
            $updated = false;
            if ($offer->qty >= $request->quantity) {
                // Everything is valid so add the data

                // If The same order exists for the same user and it's pending update the quantity of it
                if ($offer_order) {
                    $offer_order->qty = $offer_order->qty + $qty;
                    $offer_order->update();
                    $updated = true;
                } else { // Check If an order exists with the same offer id with the same user
                    OfferOrder::create([
                        'offer_id' => $request->offer_id,
                        'want_offer_id' => Auth::id(),
                        'qty' => $request->quantity,
                    ]);
                }

                return $this->success(null, 'Order ' . ($updated ? 'Updated' : 'Made') . ' , waiting admin response');
            }

            return $this->validation_errors([
                'quantity' => $this->translateWord('quantity') . ' Cannot Be Greater than existing quantiy (' . $offer->qty . ')',
            ]);
        }

        return $this->notFoundResponse();
    }
}
