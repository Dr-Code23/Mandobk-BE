<?php

namespace App\Services\Api\V1\Users;

use App\Models\User;
use App\Models\V1\Role;
use App\Models\V1\SubUser;
use App\Traits\RoleTrait;

class UserService
{

    use RoleTrait;
    public function getAllUsersInDashboardToApprove()
    {
        return User::whereIn('role_id', $this->getRolesIdsByName(config('roles.signup_roles')))
            ->whereNotIn('id', function ($query) {
                $query->select('sub_user_id')->from('sub_users');
            })
            ->join('roles', 'roles.id', 'users.role_id')
            ->get([
                'users.id as id',
                'users.full_name as full_name',
                'users.username as username',
                'roles.name as role',
                'users.created_at as created_at'
            ]);
    }
    public function approveUser($request, $user): mixed
    {
        $approve = $request->approve;
        if (
            (User::where('id', $user->id)
                ->where('status', '0')
                ->whereIn('role_id', $this->getRolesIdsByName(config('roles.signup_roles')))->value('id'))
            && !SubUser::where('sub_user_id', $user->id)->value('id')
        ) {
            if ($approve) {
                $user->update(['status' => '1']);
            } else $user->delete();
            return true;
        }
        if ($user->status == '1') true;
        return false;
    }

    /**
     * Get Users For SelectBox
     *
     * @param $request
     * @return null|array
     */
    public function getUsersForSelectBox($request): null|array
    {

        $role_name = '';
        if ($request->routeIs('roles-storehouse-all')) {
            $role_name = 'storehouse';
        } elseif ($request->routeIs('roles-pharmacy-all')) {
            $role_name = 'pharmacy';
        }
        if ($role_name) {
            $role_id = Role::where('name', $role_name)->value('id');

            $users = User::where('role_id', $role_id)->get(['id', 'full_name']);

            return $users;
        }

        return null;
    }
}
