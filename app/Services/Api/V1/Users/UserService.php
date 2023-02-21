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
            ->whereNotIn('users.id', function ($query) {
                $query->select('sub_user_id')->from('sub_users');
            })
            ->join('roles', 'roles.id', 'users.role_id')
            ->get([
                'users.id as id',
                'users.full_name as full_name',
                'users.username as username',
                'roles.name as role',
                'users.status as status',
                'users.created_at as created_at'
            ]);
    }

    /**
     * Change User Status
     *
     * @param $request
     * @param $user
     * @return mixed
     */
    public function changeUserStatus($request, $user): mixed
    {
        $status = $request->status;
        if (
            in_array($user->role_id, $this->getRolesIdsByName(config('roles.signup_roles')))
            && !SubUser::where('sub_user_id', $user->id)->value('id')
        ) {
            // Delete User And Return Success Message
            if ($status == $this->isDeleted()) {
                $user->delete();
                return true;
            }
            if ($status != $user->status) {
                $user->update(['status' => $status]);
            }
            $roleName = Role::where('id', $user->role_id)->value('name');
            $user->role = $roleName;
            return $user;
        }

        return null;
    }

    /**
     * Get Users For SelectBox
     *
     * @param $request
     * @return null|array
     */
    public function getUsersForSelectBox($request)
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

    public function getHumanResourceUsers()
    {
        return User::whereIn('role_id', $this->getRolesIdsByName(config('roles.human_resources_roles')))->get(['id', 'full_name']);
    }
}
