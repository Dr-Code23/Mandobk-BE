<?php

namespace App\Services\Api\V1\Dashboard;

use App\Http\Resources\Api\V1\Dashboard\HumanResource\HumanResourceResource;
use App\Models\User;
use App\Models\V1\HumanResource;
use App\Traits\HttpResponse;

class HumanResourceService
{
    use HttpResponse;


    /**
     * Store Or Update Human Resource
     *
     * @param $request
     * @return HumanResource|null
     */
    public function storeOrUpdate($request): HumanResource|null
    {

        $departure = $request->departure;
        $attendance = $request->attendance;
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
            if ($humanResource = HumanResource::where('date', $request->date)
                ->where('user_id', $request->user_id)
                ->first()
            ) {
                $anyChangeAccrued = false;
                if ($humanResource->attendance != $attendance) {
                    $anyChangeAccrued = true;
                    $humanResource->attendance = $attendance;
                }
                if ($humanResource->departure != $departure) {
                    $anyChangeAccrued = true;
                    $humanResource->departure = $departure;
                }
                if ($humanResource->status != $status) {
                    $anyChangeAccrued = true;
                    $humanResource->status = (int) $request->status;
                }
                if ($anyChangeAccrued) {
                    $humanResource->update();
                }
            } else {
                $humanResource = HumanResource::create([
                    'user_id' => $request->user_id,
                    'status' => (int)$status,
                    'departure' => $departure,
                    'attendance' => $attendance,
                    'date' => $request->date,
                ]);
            }
            $humanResource->full_name = $fullName_Role->full_name;
            $humanResource->role_name = $fullName_Role->role_name;
            $humanResource->role_id = $fullName_Role->role_id;

            return $humanResource;
        }
        return null;
    }
}
