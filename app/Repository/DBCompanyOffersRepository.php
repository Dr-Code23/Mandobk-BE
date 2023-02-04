<?php

namespace App\Repository;

use App\Http\Resources\Api\V1\Company\CompanyOffers\companyOfferCollection;
use App\Http\Resources\Api\V1\Company\CompanyOffers\companyOfferResource;
use App\Models\Api\V1\CompanyOffer;
use App\Models\Api\V1\Product;
use App\RepositoryInterface\CompanyOffersRepositoryInterface;
use App\Traits\HttpResponse;
use App\Traits\StringTrait;
use App\Traits\translationTrait;
use App\Traits\userTrait;

class DBCompanyOffersRepository implements CompanyOffersRepositoryInterface
{
    use userTrait;
    use HttpResponse;
    use StringTrait;
    use translationTrait;

    /**
     * @return mixed
     */
    public function allCompanyOffers()
    {
        $offers = CompanyOffer::where('company_offers.user_id', $this->getAuthenticatedUserId())
            ->join('products', 'products.id', 'company_offers.product_id')
            ->get([
                'company_offers.id as id',
                'products.id as product_id',
                'products.sc_name as scientefic_name',
                'products.com_name as commercial_name',
                'products.expire_date as expire_date',
                'company_offers.pay_method as pay_method',
                'company_offers.offer_duration as offer_duration',
                'company_offers.bonus as bonus',
                'company_offers.created_at as created_at',
                'company_offers.updated_at as updated_at',
            ]);

        return $this->resourceResponse(new companyOfferCollection($offers));
    }

    /**
     * @return mixed
     */
    public function showOneCompanyOffer($offer)
    {
        if ($offer->user_id == $this->getAuthenticatedUserId()) {
            $offer = CompanyOffer::where('company_offers.id', $offer->id)
            ->join('products', 'products.id', 'company_offers.product_id')
            ->first([
                'company_offers.id as id',
                'products.id as product_id',
                'products.sc_name as scientefic_name',
                'products.com_name as commercial_name',
                'products.expire_date as expire_date',
                'company_offers.pay_method as pay_method',
                'company_offers.offer_duration as offer_duration',
                'company_offers.bonus as bonus',
                'company_offers.created_at as created_at',
                'company_offers.updated_at as updated_at',
            ]);

            return $this->resourceResponse(new companyOfferResource($offer));
        }

        return $this->notFoundResponse();
    }

    /**
     * @return mixed
     */
    public function storeCompanyOffer($request)
    {
        // $commercial_name = $this->sanitizeString($request->commercial_name);
        // $scientefic_name = $this->sanitizeString($request->scientefic_name);

        $bonus = $this->setPercisionForFloatString($request->bonus);
        // $com_exists = false;
        // $sc_exists = false;
        // if (CompanyOffer::where('com_name', $commercial_name)->where('user_id', $this->getAuthenticatedUserId())->first(['id'])) {
        //     $com_exists = true;
        // }

        // if (CompanyOffer::where('sc_name', $scientefic_name)->where('user_id', $this->getAuthenticatedUserId())->first(['id'])) {
        //     $sc_exists = true;
        // }
        $product_id_exists = false;
        if (Product::where('id', $request->product_id)->where('user_id', $this->getAuthenticatedUserId())->first(['id'])) {
            $product_id_exists = true;
        }
        $errors = [];
        if ($product_id_exists) {
            $offer_duration = $request->offer_duration;
            if (in_array($offer_duration, ['0', '1', '2'])) {
                $pay_method = $request->pay_method;
                if (in_array($pay_method, ['1'])) {
                    // Check if the offer exists
                    if (
                        !CompanyOffer::where('product_id', $request->product_id)
                            ->where('bonus', $bonus)
                            ->where('offer_duration', $offer_duration)
                            ->where('pay_method', $pay_method)
                            ->where('user_id', $this->getAuthenticatedUserId())
                            ->first(['id'])
                    ) {
                        $offer = CompanyOffer::create([
                            // 'sc_name' => $scientefic_name,
                            // 'com_name' => $commercial_name,
                            'product_id' => $request->product_id,
                            'bonus' => $bonus,
                            'offer_duration' => $offer_duration,
                            'pay_method' => $pay_method,
                            // 'expire_date' => $request->expire_date,
                            'user_id' => $this->getAuthenticatedUserId(),
                        ]);

                        $offer = CompanyOffer::where('company_offers.id', $offer->id)
                            ->join('products', 'products.id', 'company_offers.product_id')
                            ->first([
                                'company_offers.id as id',
                                'products.id as product_id',
                                'products.sc_name as scientefic_name',
                                'products.com_name as commercial_name',
                                'products.expire_date as expire_date',
                                'company_offers.pay_method as pay_method',
                                'company_offers.offer_duration as offer_duration',
                                'company_offers.bonus as bonus',
                                'company_offers.created_at as created_at',
                                'company_offers.updated_at as updated_at',
                            ]);

                        return $this->success(new companyOfferResource($offer), 'Offer Added Successfully');
                    }
                    $errors['offer'] = $this->translateErrorMessage('offer', 'exists');
                } else {
                    $errors['pay_method'] = $this->translateErrorMessage('pay_method', 'not_found');
                }
            } else {
                $errors['offer_duration'] = $this->translateErrorMessage('offer_duration', 'not_found');
            }
        }

        // if ($com_exists) {
        //     $errors['commercial_name'] = $this->translateErrorMessage('commercial_name', 'exists');
        // }
        // if ($com_exists) {
        //     $errors['scientefic_name'] = $this->translateErrorMessage('scientefic_name', 'exists');
        // }

        if (!$product_id_exists) {
            $errors['product_id'] = $this->translateErrorMessage('product', 'exists');
        }

        return $this->validation_errors($errors);
    }

    /**
     * @return mixed
     */
    public function updateCompanyOffer($request, $offer)
    {
        if ($offer->user_id == $this->getAuthenticatedUserId()) {
            // $commercial_name = $this->sanitizeString($request->commercial_name);
            // $scientefic_name = $this->sanitizeString($request->scientefic_name);
            $bonus = $this->setPercisionForFloatString($request->bonus);
            $product_id_exists = false;
            if (Product::where('id', $request->product_id)->where('user_id', $this->getAuthenticatedUserId())->where('id', '!=', $offer->id)->first(['id'])) {
                $product_id_exists = true;
            }
            // $com_exists = false;
            // $sc_exists = false;
            // if (CompanyOffer::where('com_name', $commercial_name)->where('user_id', $this->getAuthenticatedUserId())->where('id', '!=', $offer->id)->first(['id'])) {
            //     $com_exists = true;
            // }

            // if (CompanyOffer::where('sc_name', $scientefic_name)->where('user_id', $this->getAuthenticatedUserId())->where('id', '!=', $offer->id)->first(['id'])) {
            //     $sc_exists = true;
            // }
            $errors = [];
            if ($product_id_exists) {
                $offer_duration = $request->offer_duration;
                if (in_array($offer_duration, ['0', '1', '2'])) {
                    $pay_method = $request->pay_method;
                    if (in_array($pay_method, ['1'])) {
                        $anyChangeOccured = false;
                        // if ($offer->sc_name != $scientefic_name) {
                        //     $offer->sc_name = $scientefic_name;
                        //     $anyChangeOccured = true;
                        // }
                        // if ($offer->com_name != $commercial_name) {
                        //     $offer->com_name = $commercial_name;
                        //     $anyChangeOccured = true;
                        // }
                        if ($offer->product_id != $request->product_id) {
                            $offer->product_id = $request->product_id;
                            $anyChangeOccured = true;
                        }
                        if ($offer->bonus != $bonus) {
                            $offer->bonus = $bonus;
                            $anyChangeOccured = true;
                        }
                        // if ($offer->expire_date != $request->expire_date) {
                        //     $offer->expire_date = $request->expire_date;
                        //     $anyChangeOccured = true;
                        // }
                        if ($offer->offer_duration != $request->offer_duration) {
                            $offer->offer_duration = $request->offer_duration;
                            $anyChangeOccured = true;
                        }
                        if ($offer->pay_method != $request->pay_method) {
                            $offer->pay_method = $request->pay_method;
                            $anyChangeOccured = true;
                        }
                        if ($anyChangeOccured) {
                            if (
                                !CompanyOffer::where('product_id', $request->product_id)
                                    ->where('bonus', $bonus)
                                    ->where('offer_duration', $offer_duration)
                                    ->where('pay_method', $pay_method)
                                    ->where('user_id', $this->getAuthenticatedUserId())
                                    ->where('id', '!=', $offer->id)
                                    ->first(['id'])
                            ) {
                                $offer->update();
                                $offer = CompanyOffer::where('company_offers.id', $offer->id)
                                    ->join('products', 'products.id', 'company_offers.product_id')
                                    ->first([
                                        'company_offers.id as id',
                                        'products.id as product_id',
                                        'products.sc_name as scientefic_name',
                                        'products.com_name as commercial_name',
                                        'products.expire_date as expire_date',
                                        'company_offers.pay_method as pay_method',
                                        'company_offers.offer_duration as offer_duration',
                                        'company_offers.bonus as bonus',
                                        'company_offers.created_at as created_at',
                                        'company_offers.updated_at as updated_at',
                                    ]);

                                return $this->success($offer, 'Offer Updated Successfully');
                            }
                        }

                        return $this->noContentResponse();
                    } else {
                        $errors['pay_method'] = $this->translateErrorMessage('pay_method', 'not_found');
                    }
                } else {
                    $errors['offer_duration'] = $this->translateErrorMessage('offer_duration', 'not_found');
                }
            }

            // if ($com_exists) {
            //     $errors['commercial_name'] = $this->translateErrorMessage('commercial_name', 'exists');
            // }
            // if ($com_exists) {
            //     $errors['scientefic_name'] = $this->translateErrorMessage('scientefic_name', 'exists');
            // }
            if (!$product_id_exists) {
                $errors['product_id'] = $this->translateErrorMessage('product', 'exists');
            }

            return $this->validation_errors($errors);
        }

        return $this->notFoundResponse();
    }

    /**
     * @return mixed
     */
    public function destroyCompanyOffer($offer)
    {
        if ($offer->user_id == $this->getAuthenticatedUserId()) {
            $offer->delete();

            return $this->success(msg: 'Offer Deleted Successfully');
        }

        return $this->notFoundResponse();
    }
}
