<?php

namespace App\Http\Controllers\Api\V1\Site\Pharmacy;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Site\Pharmacy\SubUserRequest;
use App\Http\Resources\Api\V1\Site\Pharmacy\SubUser\SubUserCollection;
use App\Http\Resources\Api\V1\Site\Pharmacy\SubUser\SubUserResource;
use App\Models\User;
use App\Services\Api\V1\Site\SubUsers\SubUserService;
use App\Traits\HttpResponse;
use App\Traits\RoleTrait;
use App\Traits\Translatable;
use Illuminate\Http\JsonResponse;

class SubUserController extends Controller
{
    use HttpResponse, RoleTrait, Translatable;

    public function __construct(
        private SubUserService $subUserService
    )
    {
    }

    /**
     * Show All Sub Users
     * @return JsonResponse
     */
    public function showAllSubUsers(): JsonResponse
    {
        return $this->resourceResponse(new SubUserCollection($this->subUserService->showAllSubUsers()));
    }

    /**
     * Show One Sub User
     * @param User $subUser
     * @return JsonResponse
     */
    public function showOneSubUser(User $subUser): JsonResponse
    {
        $subUser = $this->subUserService->showOneSubUser($subUser);

        if ($subUser instanceof User) {

            return $this->resourceResponse(new SubUserResource($subUser));
        }

        return $this->notFoundResponse($this->translateErrorMessage('user', 'not_found'));
    }

    /**
     * Store Sub User
     * @param SubUserRequest $request
     * @return JsonResponse
     */
    public function storeSubUser(SubUserRequest $request): JsonResponse
    {
        return $this
            ->createdResponse(new SubUserResource($this->subUserService->storeSubUser($request)));
    }

    /**
     * Update SubUser
     * @param SubUserRequest $request
     * @param User $subUser
     * @return JsonResponse
     */
    public function updateSubUser(SubUserRequest $request, User $subUser): JsonResponse
    {
        $subUser = $this->subUserService->updateSubUser($request, $subUser);

        if ($subUser instanceof User) {
            return $this->success(new SubUserResource($subUser),
                $this->translateSuccessMessage('user', 'updated'));
        }

        return $this->notFoundResponse($this->translateErrorMessage('user', 'not_found'));
    }

    /**
     * Delete Sub User
     * @param User $subUser
     * @return JsonResponse
     */
    public function destroy(User $subUser): JsonResponse
    {
        $subUserDeleted = $this->subUserService->deleteSubUser($subUser);

        if ($subUserDeleted) {
            return $this->success(null, $this->translateSuccessMessage('user', 'deleted'));
        }

        return $this->notFoundResponse($this->translateErrorMessage('user', 'not_found'));
    }
}
