<?php

namespace App\Http\Controllers\Api\V1\Users;

use App\Http\Controllers\Controller;
use App\Models\Api\V1\Role;
use App\Models\User;
use App\Traits\userTrait;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    use userTrait;

    public function getUsersForSelectBox(Request $request)
    {
        $role_name = '';
        if ($request->routeIs('roles-storehouse-all')) {
            $role_name = 'storehouse';
        } elseif ($request->routeIs('roles-pharmacy-all')) {
            $role_name = 'pharmacy';
        }
        if ($role_name) {
            $role_id = Role::where('name', $role_name)->first(['id'])->id;

            $users = User::where('role_id', $role_id)->get(['id', 'full_name']);

            return $this->resourceResponse($users);
        }

        return $this->notFoundResponse();
    }
}
