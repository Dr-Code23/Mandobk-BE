<?php

namespace App\Http\Controllers\Api\Web\V1\Roles;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Web\V1\Roles\rolesCollection;
use App\Models\Api\Web\V1\Role;
use App\Traits\HttpResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request as FacadesRequest;

class rolesController extends Controller
{
    use HttpResponse;
    /**
     * SignUp Roles For Normal Users
     * @return array
     */
    public function getSignUpRoles(){
        return $this->resourceResponse( new rolesCollection(Role::whereIn('name', config('roles.signup_roles'))->get()));
    }
}
