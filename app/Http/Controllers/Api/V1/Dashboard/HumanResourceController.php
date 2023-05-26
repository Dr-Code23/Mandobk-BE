<?php

namespace App\Http\Controllers\Api\V1\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Dashboard\HumanResourceRequest;
use App\Http\Resources\Api\V1\Dashboard\HumanResource\HumanResourceCollection;
use App\Http\Resources\Api\V1\Dashboard\HumanResource\HumanResourceResource;
use App\Models\User;
use App\Models\V1\HumanResource;
use App\Services\Api\V1\Dashboard\HumanResourceService;
use App\Traits\DateTrait;
use App\Traits\HttpResponse;
use App\Traits\Translatable;
use Illuminate\Http\JsonResponse;

class HumanResourceController extends Controller
{
    use Translatable, HttpResponse, DateTrait;

    protected HumanResource $humanResourceModel;

    public function __construct(HumanResource $humanResource)
    {
        $this->humanResourceModel = $humanResource;
    }

    /**
     * Show All Users For Human Resource
     */
    public function index(): JsonResponse
    {
        $users = User::join('roles', function ($join) {
            $join->on('roles.id', 'users.role_id')
                ->whereIn('roles.name', config('roles.human_resources_roles'));
        })
            ->join('human_resources', 'human_resources.user_id', 'users.id')
            ->orderByDesc('human_resources.date')
            ->get([
                'human_resources.id as id',
                'users.id as user_id',
                'users.full_name',
                'roles.id as role_id',
                'roles.name as role_name',
                'human_resources.date',
                'human_resources.status as status',
                'human_resources.departure',
                'human_resources.attendance',
            ]);

        return $this->resourceResponse(new HumanResourceCollection($users));
    }

    /**
     * Show One User For Human Resource
     */
    public function show(HumanResource $humanResource): JsonResponse
    {
        $user = User::join('roles', 'roles.id', 'users.role_id')
            ->where('human_resources.id', $humanResource->id)
            ->join('human_resources', 'human_resources.user_id', 'users.id')
            ->whereIn(
                config('roles_table_name').'name',
                config('roles.human_resources_roles')
            )
            ->select(
                [
                    'human_resources.id as id',
                    'users.id as user_id',
                    'users.full_name',
                    'roles.id as role_id',
                    'roles.name as role_name',
                    'human_resources.date',
                    'human_resources.departure',
                    'human_resources.attendance',
                ]
            )
            ->first();
        if ($user) {
            return $this->resourceResponse(new HumanResourceResource($user));
        }

        return $this->notFoundResponse();
    }

    /**
     * Store Or Update Human Resource
     */
    public function storeOrUpdate(
        HumanResourceRequest $request,
        HumanResourceService $humanResourceService
    ): JsonResponse {
        $humanResource = $humanResourceService->storeOrUpdate($request);
        if ($humanResource != null) {
            return $this->success(
                new HumanResourceResource($humanResource),
                $this->translateSuccessMessage('user', 'created')
            );
        }

        return $this->notFoundResponse(
            $this->translateErrorMessage('user', 'not_found')
        );
    }
}
