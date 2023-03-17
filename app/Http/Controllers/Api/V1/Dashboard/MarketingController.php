<?php

namespace App\Http\Controllers\Api\V1\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Dashboard\MarketingRequest;
use App\Http\Resources\Api\V1\Dashboard\Markting\MarktingCollection;
use App\Http\Resources\Api\V1\Dashboard\Markting\MarktingResource;
use App\Models\V1\Marketing;
use App\Services\Api\V1\Dashboard\MarktingService;
use App\Traits\FileOperationTrait;
use App\Traits\HttpResponse;
use App\Traits\StringTrait;
use App\Traits\Translatable;
use Illuminate\Http\JsonResponse;

class MarketingController extends Controller
{
    use HttpResponse, StringTrait, FileOperationTrait , Translatable;

    /**
     * Show All Ads
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return $this->resourceResponse(new MarktingCollection(Marketing::all()));
    }

    /**
     * Show One Ad
     *
     * @param Marketing $ad
     * @return JsonResponse
     */
    public function show(Marketing $ad): JsonResponse
    {
        return $this->resourceResponse(new MarktingResource($ad));
    }

    /**
     * Store Ad
     *
     * @param MarketingRequest $request
     * @param MarktingService $marketingService
     * @return JsonResponse
     */
    public function store(MarketingRequest $request, MarktingService $marketingService): JsonResponse
    {
        $ad = $marketingService->store($request);

        if ($ad instanceof Marketing) {

            return $this->createdResponse(
                new MarktingResource($ad),
                $this->translateSuccessMessage('ad' , 'created')
            );
        }

        return $this->validationErrorsResponse($ad);
    }

    /**
     * Update Ad
     *
     * @param MarketingRequest $request
     * @param Marketing $ad
     * @param MarktingService $marketingService
     * @return JsonResponse
     */
    public function update(MarketingRequest $request, Marketing $ad, MarktingService $marketingService): JsonResponse
    {
        $ad = $marketingService->update($request, $ad);

        if ($ad instanceof Marketing) {

            return $this->success(
                new MarktingResource($ad),
                $this->translateSuccessMessage('ad' , 'updated')
            );
        }

        return $this->validationErrorsResponse($ad);
    }

    /**
     * Delete Ad
     *
     * @param Marketing $ad
     * @return JsonResponse
     */
    public function destroy(Marketing $ad): JsonResponse
    {
        $this->deleteImage('markting/' . $ad->img);
        $ad->delete();

        return $this->success(
            msg: $this->translateSuccessMessage('ad' , 'deleted')
        );
    }
}
