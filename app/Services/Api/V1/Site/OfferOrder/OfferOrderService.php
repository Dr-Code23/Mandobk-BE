<?php

namespace App\Services\Api\V1\Site\OfferOrder;

use App\Models\V1\Offer;
use App\Models\V1\OfferOrder;
use App\Traits\RoleTrait;
use App\Traits\Translatable;
use Illuminate\Support\Collection;

class OfferOrderService
{
    use Translatable;
    use RoleTrait;
    public function showAllOffers(): Collection
    {
        $roleName = $this->getRoleNameForUser();
        return Offer::with(['product' => function ($query) {
            $query->withSum('product_details', 'qty');
        }, 'user'])
            ->where(
                'type',
                $roleName == 'storehouse' ? '1'
                    : (
                        in_array(
                            $roleName ,
                            [
                                'pharmacy' ,
                                'pharmacy_sub_user'
                            ]
                        )
                            ? '2'
                            : '3' // No One
                    )
            )
            ->where('to', '>=', date('Y-m-d'))
            ->where('status', '1')
            ->get();
    }

    public function order($request): string|array
    {

        $error = [];
        $offer = Offer::with(['product' => function ($query) {
            $query->select('id');
            $query->withSum('product_details', 'qty');
        }])
            ->where(
                'type',
                $this->getRoleNameForUser() == 'storehouse'
                    ? '1'
                    : ($this->getRoleNameForUser() == 'pharmacy' ? '2' : null)
            )
            ->where('id', $request->offer_id)
            ->where('to', '>=', date('Y-m-d'))
            ->first();
        // return $offer;
        if ($offer) {
            $qty = (int) $request->quantity;
            $offerOrder = OfferOrder::where('offer_id', $offer->id)
                ->where('status', '1')
                ->where('want_offer_id', auth()->id())
                ->first(['id', 'qty']);

            if ($offer->product->product_details_sum_qty >= ($qty + ($offerOrder ? $offerOrder->qty : 0))) {

                // Then Everything Is Valid

                $updated = false;
                // Everything is valid so add the data
                // If The same order exists for the same user, and it's pending , increase it's qty
                if ($offerOrder) {
                    $offerOrder->qty = $qty;
                    $offerOrder->update();
                    $updated = true;
                } else {
                    // If not , create the order
                    OfferOrder::create([
                        'offer_id' => $request->offer_id,
                        'want_offer_id' => auth()->id(),
                        'qty' => $request->quantity,
                    ]);
                }
                return  'Order ' . ($updated ? 'Updated' : 'Made') . ' , waiting admin response';
            }
            $error['quantity'][] =
                $this->translateWord('quantity')
                . ' Cannot Be Greater than existing quantity '
                . $offer->product->product_details_sum_qty;

        } else $error['offer_not_found'] = true;

        return $error;
    }
}
