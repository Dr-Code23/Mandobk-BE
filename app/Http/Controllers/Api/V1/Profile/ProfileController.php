<?php

namespace App\Http\Controllers\Api\V1\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Profile\ProfileRequest;
use App\Http\Resources\Api\V1\Profile\ProfileResource;
use App\Services\Api\V1\Profile\ProfileService;
use App\Traits\HttpResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    use HttpResponse;

    /**
     * Change Profile Information
     * @param ProfileRequest $request
     * @param ProfileService $profileService
     * @return JsonResponse
     */
    public function changeProfileInfo(ProfileRequest $request, ProfileService $profileService): JsonResponse
    {
        return $this->success(new ProfileResource($profileService->changeUserInfo($request)));
    }
}
