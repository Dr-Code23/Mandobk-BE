<?php

namespace App\Http\Controllers\Api\V1\Roles;

use App\Http\Controllers\Controller;
use App\Traits\HttpResponse;
use App\Traits\RoleTrait;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    use HttpResponse, RoleTrait;

    /**
     * Signup Roles
     *
     * @return JsonResponse
     */
    public function getSignUpRoles(): JsonResponse
    {
        return $this->resourceResponse($this->getRoleDetailsFromArrayByName(config('roles.signup_roles')));
    }

    /**
     * Get All getHumanResourceRoles.
     *
     * @return JsonResponse
     */
    public function getHumanResourceRoles(): JsonResponse
    {
        return $this->resourceResponse($this->getRoleDetailsFromArrayByName(config('roles.human_resource_roles')));
    }

    /**
     * Get All Monitor And Evaluation Roles
     *
     * @return JsonResponse
     */
    public function monitorAndEvaluationRoles(): JsonResponse
    {
        return $this->resourceResponse($this->getRoleDetailsFromArrayByName(config('roles.monitor_roles')));
    }

}
