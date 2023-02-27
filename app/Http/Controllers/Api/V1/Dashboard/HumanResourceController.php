<?php

namespace App\Http\Controllers\Api\V1\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Dashboard\HumanResourceRequest;
use App\Http\Resources\Api\V1\Dashboard\HumanResource\HumanResourceCollection;
use App\Http\Resources\Api\V1\Dashboard\HumanResource\HumanResourceResource;
use App\Models\User;
use App\Models\V1\HumanResource;
use App\Traits\DateTrait;
use App\Traits\HttpResponse;
use App\Traits\Translatable;

class HumanResourceController extends Controller
{
    use Translatable, HttpResponse, DateTrait;

    /**
     * @var HumanResource
     */
    protected HumanResource $humanResourceModel;

    /**
     * @param HumanResource $humanResource
     */
    public function __construct(HumanResource $humanResource)
    {
        $this->humanResourceModel = $humanResource;
    }

    /**
     * Summary of index.
     *
     * @return \Illuminate\Http\JsonResponse|array
     */
    public function index(): \Illuminate\Http\JsonResponse|array
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
     * @param HumanResource $humanResource
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(HumanResource $humanResource)
    {
        $user = User::join('roles', 'roles.id', 'users.role_id')
            // ->where('users.id', $human->id)
            ->where('human_resources.id', $humanResource->id)
            ->join('human_resources', 'human_resources.user_id', 'users.id')
            ->whereIn(config('roles_table_name') . 'name', config('roles.human_resources_roles'))
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
     * @param HumanResourceRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeOrUpdate(HumanResourceRequest $request): \Illuminate\Http\JsonResponse
    {

        $departure = $request->departure;
        $attendance = $request->attendance;
        $date = $request->date;
        $status = $request->status;

        if ($status != 0) {
            $departure = null;
            $attendance = null;
        }
        // Check if the user is not CEO
        $user = User::find($request->user_id)->join('roles', function ($join) {
            $join->on('roles.id', 'users.role_id')
                ->whereIn('roles.name', config('roles.human_resources_roles'));
        })
            ->where('users.id', $request->user_id)
            ->first(['roles.name as role_name']);
        if ($user && $user->role_name != 'ceo') {
            $fullName_Role = User::where('users.id', $request->user_id)
                ->join('roles', 'roles.id', 'users.role_id')
                ->select(['users.full_name', 'roles.id as role_id', 'roles.name as role_name'])
                ->first();
            if ($human_resource = HumanResource::where('date', $request->date)->where('user_id', $request->user_id)->first()) {
                $anyChangeOccrued = false;
                if ($human_resource->attendance != $attendance) {
                    $anyChangeOccrued = true;
                    $human_resource->attendance = $attendance;
                }
                if ($human_resource->departure != $departure) {
                    $anyChangeOccrued = true;
                    $human_resource->departure = $departure;
                }
                if ($human_resource->status != $status) {
                    $anyChangeOccrued = true;
                    $human_resource->status = (int) $request->status;
                }
                if ($anyChangeOccrued) {
                    $human_resource->update();
                }
                $human_resource->full_name = $fullName_Role->full_name;
                $human_resource->role_name = $fullName_Role->role_name;
                $human_resource->role_id = $fullName_Role->role_id;

                return $this->success(new HumanResourceResource($human_resource), 'Resource Updated Successfully');
            }
            $human_resource = HumanResource::create([
                'user_id' => $request->user_id,
                'status' => (int)$status,
                'departure' => $departure,
                'attendance' => $attendance,
                'date' => $request->date,
            ]);
            $human_resource->full_name = $fullName_Role->full_name;
            $human_resource->role_name = $fullName_Role->role_name;
            $human_resource->role_id = $fullName_Role->role_id;
        }
        return $this->success(new HumanResourceResource($human_resource), 'Resource Created Successfully');
    }
}
