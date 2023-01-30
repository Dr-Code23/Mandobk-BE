<?php

namespace App\Http\Controllers\Api\Web\V1\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Web\V1\Dashboard\humanResourceRequest;
use App\Http\Resources\Api\Web\V1\Dashboard\HumanResource\humanResourceCollection;
use App\Http\Resources\Api\Web\V1\Dashboard\HumanResource\humanResourceResource;
use App\Models\Api\Web\V1\HumanResource;
use App\Models\User;
use App\Traits\dateTrait;
use App\Traits\HttpResponse;
use App\Traits\translationTrait;

class humanResourceController extends Controller
{
    use translationTrait;
    use HttpResponse;
    use dateTrait;

    /**
     * All Users.
     *
     * @return array
     */
    public function index()
    {
        $users = User::join(config('roles.table_name'), config('roles.table_name').'.id', 'users.role_id')
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
            return $this->resourceResponse(new humanResourceCollection($users));
        }

        $this->notFoundResponse();
    }

    public function show(User $user)
    {
        $user = User::join(config('roles.table_name'), config('roles.table_name').'.id', 'users.role_id')
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
            return $this->resourceResponse(new humanResourceResource($user));
        }

        $this->notFoundResponse();
    }

    public function storeOrUpdate(humanResourceRequest $request)
    {
        // Check if the user is not CEO
        $user = User::find($request->user_id)->join(config('roles.table_name'), 'users.role_id', config('roles.table_name').'.id')
            ->where('users.id', $request->user_id)
            ->select(['roles.name as role_name'])->first();
        if ($user && $user->role_name != 'ceo') {
            $fullName_Role = User::where('users.id', $request->user_id)->join(config('roles.table_name'), config('roles.table_name').'.id', 'users.role_id')
                ->select(['users.full_name', config('roles.table_name').'.name as role_name'])
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

                    return $this->success(new humanResourceResource($human_resource), 'Resource Updated Successfully');
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

            return $this->success(new humanResourceResource($human_resource), 'Resource Created Successfully');
        }
        $this->notFoundResponse();
    }
}
