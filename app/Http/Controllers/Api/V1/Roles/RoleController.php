<?php

namespace App\Http\Controllers\Api\V1\Roles;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Roles\RoleCollection;
use App\Models\V1\Role;
use App\Traits\HttpResponse;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    use HttpResponse;

    /**
     * Signup Roles
     *
     * @return JsonResponse
     */
    public function getSignUpRoles(): JsonResponse
    {
        return $this->resourceResponse(new RoleCollection(Role::whereIn('name', config('roles.signup_roles'))->get(['id', 'name'])));
    }

    /**
     * Get All getHumanResourceRoles.
     *
     * @return JsonResponse
     */
    public function getHumanResourceRoles(): JsonResponse
    {
        return $this->resourceResponse(new RoleCollection(Role::whereIn('name', config('roles.human_resource_roles'))->get(['id', 'name'])));
    }

    /**
     * Get All Monitor And Evaluation Roles
     *
     * @return JsonResponse
     */
    public function monitorAndEvaluationRoles(): JsonResponse
    {
        return $this->resourceResponse(new RoleCollection(
            Role::whereIn('name', config('roles.monitor_roles'))->get(['id', 'name'])
        ));
    }
}
