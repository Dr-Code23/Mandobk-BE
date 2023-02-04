<?php

namespace App\Http\Controllers\Api\Web\V1\Roles;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Web\V1\Roles\rolesCollection;
use App\Models\Api\V1\Role;
use App\Traits\HttpResponse;

class rolesController extends Controller
{
    use HttpResponse;

    /**
     * SignUp Roles For Normal Users.
     *
     * @return array
     */
    public function getSignUpRoles()
    {
        return $this->resourceResponse(new rolesCollection(Role::whereIn('name', config('roles.signup_roles'))->get(['id', 'name'])));
    }

    public function getHumanResourceRoles()
    {
        return $this->resourceResponse(new rolesCollection(Role::whereIn('name', config('roles.human_resource_roles'))->get(['name'])));
    }
}
