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
     */
    public function getSignUpRoles(): JsonResponse
    {
        return $this->resourceResponse(
            $this->getRoleDetailsFromArrayByName(
                config('roles.signup_roles'),
                true
            )
        );
    }

    /**
     * Get All getHumanResourceRoles.
     */
    public function getHumanResourceRoles(): JsonResponse
    {
        return $this->resourceResponse(
            $this->getRoleDetailsFromArrayByName(
                config('roles.human_resource_roles'),
                true
            )
        );
    }

    /**
     * Get All Monitor And Evaluation Roles
     */
    public function monitorAndEvaluationRoles(): JsonResponse
    {
        return $this->resourceResponse(
            $this->getRoleDetailsFromArrayByName(
                config('roles.monitor_roles'),
                true
            )
        );
    }
}
