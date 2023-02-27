<?php

namespace App\Http\Controllers\Api\V1\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Dashboard\MarktingRequest;
use App\Http\Resources\Api\V1\Dashboard\Markting\MarktingCollection;
use App\Http\Resources\Api\V1\Dashboard\Markting\MarktingResource;
use App\Models\V1\Markting;
use App\Services\Api\V1\Dashboard\MarktingService;
use App\Traits\FileOperationTrait;
use App\Traits\HttpResponse;
use App\Traits\StringTrait;
use Illuminate\Http\JsonResponse;

class MarktingController extends Controller
{
    use HttpResponse, StringTrait, FileOperationTrait;

    /**
     * Show All Ads
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return $this->resourceResponse(new MarktingCollection(Markting::all()));
    }

    /**
     * Show One Ad
     *
     * @param Markting $ad
     * @return JsonResponse
     */
    public function show(Markting $ad): JsonResponse
    {
        return $this->resourceResponse(new MarktingResource($ad));
    }

    /**
     * Store Ad
     *
     * @param MarktingRequest $request
     * @param MarktingService $marktingService
     * @return JsonResponse
     */
    public function store(MarktingRequest $request, MarktingService $marktingService): JsonResponse
    {
        $ad = $marktingService->store($request);

        if ($ad instanceof Markting) {
            return $this->success(new MarktingResource($ad), 'Ad Created Successfully');
        }

        return $this->validation_errors($ad);
    }

    /**
     * Update Ad
     *
     * @param MarktingRequest $request
     * @param Markting $ad
     * @param MarktingService $marktingService
     * @return JsonResponse
     */
    public function update(MarktingRequest $request, Markting $ad, MarktingService $marktingService): JsonResponse
    {
        $ad = $marktingService->update($request, $ad);

        if ($ad instanceof Markting)
            return $this->success(new MarktingResource($ad), 'Ad Updated Successfully');

        return $this->validation_errors($ad);
    }

    /**
     * Delete Ad
     *
     * @param Markting $ad
     * @return JsonResponse
     */
    public function destroy(Markting $ad): JsonResponse
    {
        $this->deleteImage('markting/' . $ad->img);
        $ad->delete();

        return $this->success(msg: 'Ad Deleted Successfully');
    }
}
