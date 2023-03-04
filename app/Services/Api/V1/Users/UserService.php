<?php

namespace App\Services\Api\V1\Users;

use App\Models\User;
use App\Models\V1\SubUser;
use App\Models\V1\VisitorRecipe;
use App\Traits\RoleTrait;
use Illuminate\Support\Collection;

class UserService
{

    use RoleTrait;

    /**
     * Get All Public Users To Manage In Dashboard
     * @return Collection
     */
    public function getAllUsersToManage(): Collection
    {
        return User::whereIn('role_id', $this->getRolesIdsByName(config('roles.signup_roles')))
            ->whereNotIn('users.id', function ($query) {
                $query->select('sub_user_id')
                    ->from('sub_users');
            })
            ->join('roles', 'roles.id', 'users.role_id')
            ->latest()
            ->get([
                'users.id as id',
                'users.full_name as full_name',
                'users.username as username',
                'roles.name as role',
                'users.status as status',
                'users.phone as phone',
                'users.created_at as created_at'
            ]);
    }

    /**
     * Change User Status
     *
     * @param $request
     * @param $user
     * @return User|null|boolean
     */
    public function changeUserStatus($request, $user): User|null|bool
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
                $user->update(['status' => $status . '']);
                info($user);
            }
            $user->role = $this->getRoleNameById($user->role_id);;

            return $user;
        }

        return null;
    }

    /**
     * Get Users For SelectBox
     *
     * @param $request
     * @return null|Collection
     */
    public function getUsersForSelectBox($request): Collection|null
    {
        $role_name = '';
        if ($request->routeIs('roles-storehouse-all')) {
            $role_name = 'storehouse';
        } elseif ($request->routeIs('roles-pharmacy-all')) {
            $role_name = 'pharmacy';
        }
        if ($role_name) {
            // $role_id = Role::where('name', $role_name)->value('id');
            $role_id = $this->getRoleIdByName($role_name);
            return User::where('role_id', $role_id)->get(['id', 'full_name']);
        }

        return null;
    }

    public function getHumanResourceUsers()
    {
        return User::whereIn('role_id', $this->getRolesIdsByName(config('roles.human_resources_roles')))->get(['id', 'full_name']);
    }

    public function registerNewVisitor($request)
    {

        $visitor = User::create($request->validated() + [
            'role_id' => $this->getRoleIdByName('visitor'),
            'full_name' => $request->name,
            'status' => '1'
        ]);

        $visitorInfo = VisitorRecipe::create([
            'visitor_id' => $visitor->id,
            'alias' => $request->alias,
            'details' => [],
            'random_number' => $this->generateRandomNumberForVisitor(),
        ]);

        $visitorInfo->name = $request->name;
        $visitorInfo->username = $request->username;
        $visitorInfo->phone = $request->phone;

        return $visitorInfo;
    }


    /**
     * Restore Visitor Random Numbers
     * @param $request
     * @return array|bool
     */
    public function forgotVisitorRandomNumber($request): array|bool
    {
        $handle = $request->input('handle');
        if (
            $visitor = User::where(function ($query) use ($handle) {
                $query->where('username', $handle)
                    ->orWhere('phone', $handle);
            })->first(['id'])
        ) {
            $randomNumbers = [];
            $cnt = 0;
            foreach (VisitorRecipe::where('visitor_id', $visitor->id)->get(['random_number', 'alias']) as $recipe) {
                $randomNumbers[$cnt]['random_number'] = $recipe->random_number;
                $randomNumbers[$cnt]['alias'] = $recipe->alias;
                ++$cnt;
            }

            return $randomNumbers;
        }

        return false;
    }


    /**
     * @param $request
     * @return array|VisitorRecipe
     */
    public function addRandomNumberForVisitor($request): VisitorRecipe|array
    {
        $visitor = User::where('username', $request->username)
            ->where('role_id', $this->getRoleIdByName('visitor'))
            ->first(['id', 'username']);

        $errors = [];
        if ($visitor) {
            // Search In Visitor Recipes For Visitor

            $recipe = VisitorRecipe::where('visitor_id', $visitor->id)
                ->where('alias', $request->alias)->first(['id', 'alias']);
            if (!$recipe) {
                return VisitorRecipe::create([
                    'visitor_id' => $visitor->id,
                    'alias' => $request->alias,
                    'random_number' => $this->generateRandomNumberForVisitor(),
                    'details' => []
                ]);
            } else $errors['alias'] = ['Alias Already Exists'];
        } else $errors['username'] = ['Username Not Exists'];

        return $errors;
    }
}
