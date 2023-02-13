<?php

namespace App\Http\Controllers\Api\V1\Roles;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Roles\roleCollection;
use App\Models\V1\Role;
use App\Traits\HttpResponse;

class roleController extends Controller
{
    use HttpResponse;

    /**
     * Summary of getSignUpRoles.
     *
     * @return array
     */
    public function getSignUpRoles()
    {
        return $this->resourceResponse(new roleCollection(Role::whereIn('name', config('roles.signup_roles'))->get(['id', 'name'])));
    }

    /**
     * Summary of getHumanResourceRoles.
     *
     * @return array
     */
    public function getHumanResourceRoles()
    {
        return $this->resourceResponse(new roleCollection(Role::whereIn('name', config('roles.human_resource_roles'))->get(['id', 'name'])));
    }
}