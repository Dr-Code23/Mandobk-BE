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
use App\Traits\TranslationTrait;

class HumanResourceController extends Controller
{
    use TranslationTrait;
    use HttpResponse;
    use DateTrait;

    /**
     * Summary of index.
     *
     * @return \Illuminate\Http\JsonResponse|array
     */
    public function index()
    {
        $users = User::join('roles', 'roles.id', 'users.role_id')
            ->join('human_resources', 'human_resources.user_id', 'users.id')
            ->whereIn(config('roles_table_name').'name', config('roles.human_resources_roles'))
            ->orderBy('human_resources.date', 'DESC')
            ->select(
                [
                    'users.id',
                    'users.full_name',
                    'roles.name as role_name',
                    'human_resources.date',
                    'human_resources.departure',
                    'human_resources.attendance',
                ]
            )
            ->get();
        if ($users) {
            return $this->resourceResponse(new HumanResourceCollection($users));
        }

        return $this->notFoundResponse();
    }

    public function show(User $user)
    {
        $user = User::join('roles', 'roles.id', 'users.role_id')
            ->where('users.id', $user->id)
            ->join('human_resources', 'human_resources.user_id', 'users.id')
            ->whereIn(config('roles_table_name').'name', config('roles.human_resources_roles'))
            ->select(
                [
                    'users.id',
                    'users.full_name',
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

    public function storeOrUpdate(HumanResourceRequest $request)
    {
        // Check if the user is not CEO
        $user = User::find($request->user_id)->join('roles', 'users.role_id', 'roles.id')
            ->where('users.id', $request->user_id)
            ->select(['roles.name as role_name'])->first();
        if ($user && $user->role_name != 'ceo') {
            $fullName_Role = User::where('users.id', $request->user_id)->join('roles', 'roles.id', 'users.role_id')
                ->select(['users.full_name', 'roles.name as role_name'])
                ->first();
            if ($human_resource = HumanResource::where('date', $request->date)->where('user_id', $request->user_id)->first()) {
                $anyChangeOccrued = false;
                if ($human_resource->attendance != $request->attendance) {
                    $anyChangeOccrued = true;
                    $human_resource->attendance = $request->attendance;
                }
                if ($human_resource->departure != $request->departure) {
                    $anyChangeOccrued = true;
                    $human_resource->departure = $request->departure;
                }
                if ($human_resource->status != $request->status) {
                    $anyChangeOccrued = true;
                    $human_resource->status = $request->status;
                }
                if ($anyChangeOccrued) {
                    $human_resource->update();
                    $human_resource->full_name = $fullName_Role->full_name;

                    return $this->success(new HumanResourceResource($human_resource), 'Resource Updated Successfully');
                }

                return $this->noContentResponse();
            }
            $human_resource = HumanResource::create([
                'user_id' => $request->user_id,
                'status' => $request->status,
                'departure' => $request->departure,
                'attendance' => $request->attendance,
                'date' => $request->date,
            ]);
            $human_resource->full_name = $fullName_Role->full_name;
            $human_resource->role_name = $fullName_Role->full_name;

            return $this->success(new HumanResourceResource($human_resource), 'Resource Created Successfully');
        }
        $this->notFoundResponse();
    }
}
