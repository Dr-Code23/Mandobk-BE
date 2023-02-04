<?php

namespace App\Repository;

use App\Http\Resources\Api\Web\V1\Company\CompanyOffers\companyOfferCollection;
use App\Http\Resources\Api\Web\V1\Company\CompanyOffers\companyOfferResource;
use App\Models\Api\V1\CompanyOffer;
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
    private string $file_name = 'Company/CompanyOffers/companyOffersTranslationFile.';

    /**
     * @return mixed
     */
    public function allCompanyOffers()
    {
        return $this->resourceResponse(new companyOfferCollection(CompanyOffer::where('user_id', $this->getAuthenticatedUserId())->get()));
    }

    /**
     * @return mixed
     */
    public function showOneCompanyOffer($offer)
    {
        if ($offer->user_id == $this->getAuthenticatedUserId()) {
            return $this->resourceResponse(new companyOfferResource($offer));
        }

        return $this->notFoundResponse();
    }

    /**
     * @return mixed
     */
    public function storeCompanyOffer($request)
    {
        $commercial_name = $this->sanitizeString($request->commercial_name);
        $scientefic_name = $this->sanitizeString($request->scientefic_name);
        $bonus = $this->setPercisionForFloatString($request->bonus);
        $com_exists = false;
        $sc_exists = false;
        if (CompanyOffer::where('com_name', $commercial_name)->where('user_id', $this->getAuthenticatedUserId())->first(['id'])) {
            $com_exists = true;
        }

        if (CompanyOffer::where('sc_name', $scientefic_name)->where('user_id', $this->getAuthenticatedUserId())->first(['id'])) {
            $sc_exists = true;
        }
        $errors = [];
        if (!$com_exists && !$sc_exists) {
            $offer_duration = $request->offer_duration;
            if (in_array($offer_duration, ['0', '1', '2'])) {
                $pay_method = $request->pay_method;
                if (in_array($pay_method, ['1'])) {
                    $offer = CompanyOffer::create([
                        'sc_name' => $scientefic_name,
                        'com_name' => $commercial_name,
                        'bonus' => $bonus,
                        'offer_duration' => $offer_duration,
                        'pay_method' => $pay_method,
                        'expire_date' => $request->expire_date,
                        'user_id' => $this->getAuthenticatedUserId(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    return $this->success(new companyOfferResource($offer), 'Offer Added Successfully');
                } else {
                    $errors['pay_method'] = $this->translateErrorMessage('pay_method', 'not_found');
                }
            } else {
                $errors['offer_duration'] = $this->translateErrorMessage('offer_duration', 'not_found');
            }
        }

        if ($com_exists) {
            $errors['commercial_name'] = $this->translateErrorMessage('commercial_name', 'exists');
        }
        if ($com_exists) {
            $errors['scientefic_name'] = $this->translateErrorMessage('scientefic_name', 'exists');
        }

        return $this->validation_errors($errors);
    }

    /**
     * @return mixed
     */
    public function updateCompanyOffer($request, $offer)
    {
        if ($offer->user_id == $this->getAuthenticatedUserId()) {
            $commercial_name = $this->sanitizeString($request->commercial_name);
            $scientefic_name = $this->sanitizeString($request->scientefic_name);
            $bonus = $this->setPercisionForFloatString($request->bonus);
            $com_exists = false;
            $sc_exists = false;
            if (CompanyOffer::where('com_name', $commercial_name)->where('user_id', $this->getAuthenticatedUserId())->where('id', '!=', $offer->id)->first(['id'])) {
                $com_exists = true;
            }

            if (CompanyOffer::where('sc_name', $scientefic_name)->where('user_id', $this->getAuthenticatedUserId())->where('id', '!=', $offer->id)->first(['id'])) {
                $sc_exists = true;
            }
            $errors = [];
            if (!$com_exists && !$sc_exists) {
                $offer_duration = $request->offer_duration;
                if (in_array($offer_duration, ['0', '1', '2'])) {
                    $pay_method = $request->pay_method;
                    if (in_array($pay_method, ['1'])) {
                        $anyChangeOccured = false;
                        if ($offer->sc_name != $scientefic_name) {
                            $offer->sc_name = $scientefic_name;
                            $anyChangeOccured = true;
                        }
                        if ($offer->com_name != $commercial_name) {
                            $offer->com_name = $commercial_name;
                            $anyChangeOccured = true;
                        }
                        if ($offer->bonus != $bonus) {
                            $offer->bonus = $bonus;
                            $anyChangeOccured = true;
                        }
                        if ($offer->expire_date != $request->expire_date) {
                            $offer->expire_date = $request->expire_date;
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

            if ($com_exists) {
                $errors['commercial_name'] = $this->translateErrorMessage('commercial_name', 'exists');
            }
            if ($com_exists) {
                $errors['scientefic_name'] = $this->translateErrorMessage('scientefic_name', 'exists');
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
