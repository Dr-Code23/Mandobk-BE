<?php

namespace App\Repository;

use App\Http\Resources\Api\V1\Offers\OfferCollection;
use App\Http\Resources\Api\V1\Offers\OfferResource;
use App\Models\V1\Offer;
use App\Models\V1\Product;
use App\Models\V1\Role;
use App\RepositoryInterface\OfferRepositoryInterface;
use App\Traits\dateTrait;
use App\Traits\HttpResponse;
use App\Traits\translationTrait;
use App\Traits\userTrait;
use Illuminate\Support\Facades\Auth;

class DBOfferRepository implements OfferRepositoryInterface
{
    use userTrait;
    use HttpResponse;
    use translationTrait;
    use dateTrait;
    private Offer $offerModel;
    private Role $roleModel;
    private Product $productModel;

    public function __construct(Offer $offer, Role $role, Product $product)
    {
        $this->offerModel = $offer;
        $this->roleModel = $role;
        $this->productModel = $product;
    }

    /**
     * @return mixed
     */
    public function allOffers($request)
    {
        $offers = $this->offerModel->join('products', 'products.id', 'offers.product_id')
            ->where('type', $this->roleModel->where('id', $this->getAuthenticatedUserInformation()->role_id)->first(['name'])->name == 'company' ? '1' : '2')
            ->whereIn('offers.user_id', $this->getSubUsersForAuthenticatedUser())
            ->where(function ($query) use ($request) {
                if ($request->has('type')) {
                    $type = $request->input('type');
                    if ($type == 'day') {
                        $query->where('offers.offer_duration', '0');
                    } elseif ($type == 'week') {
                        $query->where('offers.offer_duration', '1');
                    } elseif ($type == 'cheek') {
                        $query->where('offers.offer_duration', '2');
                    }
                }
            })
            ->get([
                'offers.id as id',
                'products.id as product_id',
                'products.sc_name as scientific_name',
                'products.com_name as commercial_name',
                'products.expire_date as expire_date',
                'offers.works_untill as works_untill',
                'offers.pay_method as pay_method',
                'offers.offer_duration as offer_duration',
                'offers.bonus as bonus',
                'offers.created_at as created_at',
                'offers.updated_at as updated_at',
            ]);
        if ($offers) {
            return $this->resourceResponse(new OfferCollection($offers));
        }

        return $this->resourceResponse(['data' => []]);
    }

    /**
     * @param mixed $offer
     *
     * @return mixed
     */
    public function showOneOffer($offer)
    {
        $offer = $this->offerModel->where('offers.id', $offer->id)
            ->join('products', 'products.id', 'offers.product_id')
            ->where('type', $this->roleModel->where('id', $this->getAuthenticatedUserInformation()->role_id)->first(['name'])->name == 'company' ? '1' : '2')
            ->whereIn('offers.user_id', $this->getSubUsersForAuthenticatedUser())

            ->first([
                'offers.id as id',
                'products.id as product_id',
                'products.sc_name as scientific_name',
                'products.com_name as commercial_name',
                'products.expire_date as expire_date',
                'offers.works_untill as works_untill',
                'offers.pay_method as pay_method',
                'offers.offer_duration as offer_duration',
                'offers.bonus as bonus',
                'offers.created_at as created_at',
                'offers.updated_at as updated_at',
            ]);

        if ($offer) {
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
        $offer_exists = false;

        // return 'Good';
        if (
            $this->productModel->where('id', $request->product_id)
            ->where('user_id', Auth::id())
            ->first(['id'])
        ) {
            $product_id_exists = true;
        }
        // var_dump($product_id_exists);

        // return;
        if (
            $this->offerModel->where('product_id', $request->product_id)
            ->where('user_id', Auth::id())
            ->where('pay_method', $request->pay_method)
            ->where('offer_duration', $request->offer_duration)
            ->where('bonus', $bonus)
            ->where('type', $this->roleModel->where('id', $this->getAuthenticatedUserInformation()->role_id)->first(['name'])->name == 'company' ? '1' : '2')
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
                        // return $this->addDaysToDate($offer_duration == '0' ? 1 : ($offer_duration == '1' ? 7 : 1000));
                        $offer = $this->offerModel->create([
                            'product_id' => $request->product_id,
                            'bonus' => $bonus,
                            'offer_duration' => $offer_duration,
                            'pay_method' => $pay_method,
                            'works_untill' => $this->addDaysToDate($offer_duration == '0' ? 1 : ($offer_duration == '1' ? 7 : 1000)),
                            'type' => $this->roleModel->where('id', $this->getAuthenticatedUserInformation()->role_id)->first(['name'])->name == 'company' ? '1' : '2',
                            'user_id' => Auth::id(),
                        ]);

                        $offer = $this->offerModel->where('offers.id', $offer->id)
                            ->join('products', 'products.id', 'offers.product_id')
                            ->where('type', $this->roleModel->where('id', $this->getAuthenticatedUserInformation()->role_id)->first(['name'])->name == 'company' ? '1' : '2')
                            ->first([
                                'offers.id as id',
                                'products.id as product_id',
                                'products.sc_name as scientific_name',
                                'products.com_name as commercial_name',
                                'products.expire_date as expire_date',
                                'offers.works_untill',
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
        if ($offer->user_id == Auth::id()) {
            $bonus = $this->setPercisionForFloatString($request->bonus);
            $product_id_exists = false;
            if (
                $this->productModel->where('id', $request->product_id)
                ->where('user_id', Auth::id())
                ->first(['id'])
            ) {
                $product_id_exists = true;
            }
            $offer_exists = false;
            if (
                $this->offerModel->where('product_id', $request->product_id)
                ->where('user_id', Auth::id())
                ->where('pay_method', $request->pay_method)
                ->where('offer_duration', $request->offer_duration)
                ->where('bonus', $bonus)
                ->where('type', $this->roleModel->where('id', $this->getAuthenticatedUserInformation()->role_id)->first(['name'])->name == 'company' ? '1' : '2')
                ->where('id', '!=', $offer->id)
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
                                $duration = $offer->offer_duration;
                                // Add To the Order Created at date
                                $offer->works_untill = $this->addDaysToDate($duration == '0' ? 1 : ($duration == '1' ? 7 : 1000), $offer->created_at);
                                $anyChangeOccured = true;
                            }
                            if ($offer->pay_method != $request->pay_method) {
                                $offer->pay_method = $request->pay_method;
                                $anyChangeOccured = true;
                            }
                            if ($anyChangeOccured) {
                                $offer->update();
                                $offer = $this->offerModel->where('offers.id', $offer->id)
                                    ->join('products', 'products.id', 'offers.product_id')
                                    ->first([
                                        'offers.id as id',
                                        'products.id as product_id',
                                        'products.sc_name as scientific_name',
                                        'products.com_name as commercial_name',
                                        'products.expire_date as expire_date',
                                        'offers.works_untill',
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
        if ($offer->user_id == Auth::id()) {
            $offer->delete();

            return $this->success(msg: 'Offer Deleted Successfully');
        }

        return $this->notFoundResponse();
    }

    /**
     * @return mixed
     */
    public function getAllOfferDurations()
    {
        return $this->resourceResponse([
            [
                'id' => '0',
                'name' => $this->translateWord('day'),
            ],
            [
                'id' => '1',
                'name' => $this->translateWord('week'),
            ],
            [
                'id' => '2',
                'name' => $this->translateWord('cheek'),
            ],
        ]);
    }
}
