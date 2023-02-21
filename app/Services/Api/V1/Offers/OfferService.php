<?php

namespace App\Services\Api\V1\Offers;

use App\Models\V1\Offer;
use App\Models\V1\PayMethod;
use App\Models\V1\Product;
use App\Models\V1\Role;
use App\Traits\RoleTrait;
use App\Traits\Translatable;
use App\Traits\UserTrait;
use Illuminate\Support\Facades\Auth;

use Illuminate\Database\Eloquent\Collection;

class OfferService
{
    use UserTrait;
    use RoleTrait;
    use Translatable;
    private string $productRelation = 'product:id,com_name,sc_name,con,sel_price,bonus';
    public function __construct(
        protected Offer $offerModel,
        protected Role $roleModel,
        protected Product $productModel,
    ) {
    }

    /**
     * Fetch All Offers
     *
     * @return Collection
     */
    public function allOffers(): Collection
    {
        return Offer::where('user_id', Auth::id())
            ->where('type', $this->roleNameIn(['company']) ? '1' : '2')
            ->with($this->productRelation)
            ->get();
    }

    /**
     * Show One Offer
     *
     * @param Offer $offer
     * @return mixed
     */
    public function show($offer)
    {
        if ($offer->user_id == Auth::id() && $offer->type == ($this->roleNameIn(['company']) ? '1' : '2'))
            return $offer->load($this->productRelation);
        else return null;
    }

    public function store($request)
    {

        $errors = [];
        if (
            !$this->productModel->where('id', $request->product_id)
                ->where('user_id', Auth::id())
                ->first(['id'])
        ) {
            $errors['product'] = $this->translateErrorMessage('product', 'not_exists');
        }

        if (
            $this->offerModel->where('product_id', $request->product_id)
            ->where('user_id', Auth::id())
            ->where('from', $request->start_date)
            ->where('to', $request->end_date)
            ->first(['id'])
        ) {
            $errors['offer'] = $this->translateErrorMessage('offer', 'exists');
        }

        if (!PayMethod::where('id', $request->pay_method_id)->first('id'))
            $errors['pay_method'] = $this->translateErrorMessage('pay_method', 'not_exists');

        if (!$errors) {
            $offer = $this->offerModel->create([
                'product_id' => $request->product_id,
                'pay_method' => $request->pay_method_id,
                'type' => $this->roleNameIn(['company']) ? '1' : '2',
                'user_id' => Auth::id(),
                'from' => $request->start_date,
                'to' => $request->end_date,
                'status' => '1'
            ]);
            $offer->load($this->productRelation);
            return $offer;
        }

        $errors['error'] = true;
        return $errors;
    }


    public function changeOfferStatus($request, $offer)
    {
        $errors = [];

        if (
            $offer->user_id != Auth::id()
            || $offer->type != ($this->roleNameIn(['company']) ? '1' : '2')
        ) {
            $errors['offer'] = $this->translateErrorMessage('offer', 'not_exists');
        }

        if (!$errors) {
            if ($offer->status != $request->status) {
                $offer->status = $request->status;
                $offer->update();
            }
            $offer->load($this->productRelation);
            return $offer;
        }

        $errors['error'] = true;
        return $errors;
    }

    public function destroy($offer): bool
    {
        if ($offer->user_id == Auth::id()) {
            $offer->delete();
            return true;
        }
        return false;
    }
}
