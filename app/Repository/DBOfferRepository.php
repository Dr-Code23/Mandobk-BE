<?php

namespace App\Repository;

use App\Http\Resources\Api\V1\Offers\OfferCollection;
use App\Http\Resources\Api\V1\Offers\OfferResource;
use App\Models\Api\V1\Offer;
use App\Models\Api\V1\Product;
use App\Models\Api\V1\Role;
use App\RepositoryInterface\OfferRepositoryInterface;
use App\Traits\HttpResponse;
use App\Traits\translationTrait;
use App\Traits\userTrait;

class DBOfferRepository implements OfferRepositoryInterface
{
    use userTrait;
    use HttpResponse;
    use translationTrait;

    /**
     * @return mixed
     */
    public function allOffers()
    {
        $offers = Offer::where('offers.user_id', $this->getAuthenticatedUserId())
        ->join('products', 'products.id', 'offers.product_id')
        ->where('type', Role::where('id', $this->getAuthenticatedUserInformation()->role_id)->first(['name'])->name == 'company' ? '1' : '2')
        ->get([
            'offers.id as id',
            'products.id as product_id',
            'products.sc_name as scientefic_name',
            'products.com_name as commercial_name',
            'products.expire_date as expire_date',
            'offers.pay_method as pay_method',
            'offers.offer_duration as offer_duration',
            'offers.bonus as bonus',
            'offers.created_at as created_at',
            'offers.updated_at as updated_at',
        ]);

        return $this->resourceResponse(new OfferCollection($offers));
    }

    /**
     * @param mixed $offer
     *
     * @return mixed
     */
    public function showOneOffer($offer)
    {
        if ($offer->user_id == $this->getAuthenticatedUserId()) {
            $offer = Offer::where('offers.id', $offer->id)
            ->join('products', 'products.id', 'offers.product_id')
            ->where('type', Role::where('id', $this->getAuthenticatedUserInformation()->role_id)->first(['name'])->name == 'company' ? '1' : '2')
            ->first([
                'offers.id as id',
                'products.id as product_id',
                'products.sc_name as scientefic_name',
                'products.com_name as commercial_name',
                'products.expire_date as expire_date',
                'offers.pay_method as pay_method',
                'offers.offer_duration as offer_duration',
                'offers.bonus as bonus',
                'offers.created_at as created_at',
                'offers.updated_at as updated_at',
            ]);

            return $this->resourceResponse(new OfferResource($offer));
        }

        return $this->notFoundResponse();
    }

    /**
     * @param mixed $request
     *
     * @return mixed
     */
    public function storeOffer($request)
    {
        $bonus = $this->setPercisionForFloatString($request->bonus);
        $product_id_exists = false;
        if (
            Product::where('id', $request->product_id)
            ->where('user_id', $this->getAuthenticatedUserId())
            ->first(['id'])
        ) {
            $product_id_exists = true;
        }
        $offer_exists = false;
        if (
            Offer::where('product_id', $request->product_id)
                ->where('user_id', $this->getAuthenticatedUserId())
                ->where('pay_method', $request->pay_method)
                ->where('offer_duration', $request->offer_duration)
                ->where('bonus', $bonus)
                ->where('type', Role::where('id', $this->getAuthenticatedUserInformation()->role_id)->first(['name'])->name == 'company' ? '1' : '2')
                ->first(['id'])
        ) {
            $offer_exists = true;
        }

        $errors = [];
        if (!$offer_exists) {
            if ($product_id_exists) {
                $offer_duration = $request->offer_duration;
                if (in_array($offer_duration, ['0', '1', '2'])) {
                    $pay_method = $request->pay_method;
                    if (in_array($pay_method, ['1'])) {
                        // Check if the offer exists
                        $offer = Offer::create([
                            'product_id' => $request->product_id,
                            'bonus' => $bonus,
                            'offer_duration' => $offer_duration,
                            'pay_method' => $pay_method,
                            'type' => Role::where('id', $this->getAuthenticatedUserInformation()->role_id)->first(['name'])->name == 'company' ? '1' : '2',
                            'user_id' => $this->getAuthenticatedUserId(),
                        ]);

                        $offer = Offer::where('offers.id', $offer->id)
                            ->join('products', 'products.id', 'offers.product_id')
                            ->where('type', Role::where('id', $this->getAuthenticatedUserInformation()->role_id)->first(['name'])->name == 'company' ? '1' : '2')
                            ->first([
                                'offers.id as id',
                                'products.id as product_id',
                                'products.sc_name as scientefic_name',
                                'products.com_name as commercial_name',
                                'products.expire_date as expire_date',
                                'offers.pay_method as pay_method',
                                'offers.offer_duration as offer_duration',
                                'offers.bonus as bonus',
                                'offers.created_at as created_at',
                                'offers.updated_at as updated_at',
                            ]);

                        return $this->success(new OfferResource($offer), 'Offer Added Successfully');
                    } else {
                        $errors['pay_method'] = $this->translateErrorMessage('pay_method', 'not_found');
                    }
                } else {
                    $errors['offer_duration'] = $this->translateErrorMessage('offer_duration', 'not_found');
                }
            }
        }
        if ($offer_exists) {
            $errors['offer'] = $this->translateErrorMessage('offer', 'exists');
        } elseif (!$product_id_exists) {
            $errors['product_id'] = $this->translateErrorMessage('product', 'not_exists');
        }

        return $this->validation_errors($errors);
    }

    /**
     * @param mixed $request
     * @param mixed $offer
     *
     * @return mixed
     */
    public function updateOffer($request, $offer)
    {
        if ($offer->user_id == $this->getAuthenticatedUserId()) {
            $bonus = $this->setPercisionForFloatString($request->bonus);
            $product_id_exists = false;
            if (
                Product::where('id', $request->product_id)
                    ->where('user_id', $this->getAuthenticatedUserId())
                    ->where('id', '!=', $offer->id)
                    ->first(['id'])
            ) {
                $product_id_exists = true;
            }
            $offer_exists = false;
            if (
                Offer::where('product_id', $request->product_id)
                    ->where('user_id', $this->getAuthenticatedUserId())
                    ->where('pay_method', $request->pay_method)
                    ->where('offer_duration', $request->offer_duration)
                    ->where('bonus', $bonus)
                    ->where('type', Role::where('id', $this->getAuthenticatedUserInformation()->role_id)->first(['name'])->name == 'company' ? '1' : '2')
                    ->first(['id'])
            ) {
                $offer_exists = true;
            }
            $errors = [];
            if (!$offer_exists) {
                if ($product_id_exists) {
                    $offer_duration = $request->offer_duration;
                    if (in_array($offer_duration, ['0', '1', '2'])) {
                        $pay_method = $request->pay_method;
                        if (in_array($pay_method, ['1'])) {
                            $anyChangeOccured = false;
                            if ($offer->product_id != $request->product_id) {
                                $offer->product_id = $request->product_id;
                                $anyChangeOccured = true;
                            }
                            if ($offer->bonus != $bonus) {
                                $offer->bonus = $bonus;
                                $anyChangeOccured = true;
                            }
                            if ($offer->offer_duration != $request->offer_duration) {
                                $offer->offer_duration = $request->offer_duration;
                                $anyChangeOccured = true;
                            }
                            if ($offer->pay_method != $request->pay_method) {
                                $offer->pay_method = $request->pay_method;
                                $anyChangeOccured = true;
                            }
                            if ($anyChangeOccured) {
                                $offer->update();
                                $offer = Offer::where('offers.id', $offer->id)
                                    ->join('products', 'products.id', 'offers.product_id')
                                    ->first([
                                        'offers.id as id',
                                        'products.id as product_id',
                                        'products.sc_name as scientefic_name',
                                        'products.com_name as commercial_name',
                                        'products.expire_date as expire_date',
                                        'offers.pay_method as pay_method',
                                        'offers.offer_duration as offer_duration',
                                        'offers.bonus as bonus',
                                        'offers.created_at as created_at',
                                        'offers.updated_at as updated_at',
                                    ]);

                                return $this->success($offer, 'Offer Updated Successfully');
                            }

                            return $this->noContentResponse();
                        } else {
                            $errors['pay_method'] = $this->translateErrorMessage('pay_method', 'not_found');
                        }
                    } else {
                        $errors['offer_duration'] = $this->translateErrorMessage('offer_duration', 'not_found');
                    }
                }
            }
            if ($offer_exists) {
                $errors['offer'] = $this->translateErrorMessage('offer', 'exists');
            } elseif (!$product_id_exists) {
                $errors['product_id'] = $this->translateErrorMessage('product', 'not_exists');
            }

            return $this->validation_errors($errors);
        }

        return $this->notFoundResponse();
    }

    /**
     * @param mixed $offer
     *
     * @return mixed
     */
    public function destroyOffer($offer)
    {
        if ($offer->user_id == $this->getAuthenticatedUserId()) {
            $offer->delete();

            return $this->success(msg: 'Offer Deleted Successfully');
        }

        return $this->notFoundResponse();
    }

    /**
     * @return mixed
     */
    public function getAllOffersForOthers()
    {
    }

    /**
     * @return mixed
     */
    public function getAllOfferDurations()
    {
        return $this->resourceResponse([
            '0' => $this->translateWord('day'),
            '1' => $this->translateWord('week'),
            '2' => $this->translateWord('cheek'),
        ]);
    }
}
