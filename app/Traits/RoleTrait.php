<?php

namespace App\Traits;

use App\Models\V1\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

trait RoleTrait
{
    use UserTrait;

    // public function __call($member, $arguments)
    // {
    //     $argumentsCount = count($arguments);
    //     if (method_exists($this, $function = $member . $argumentsCount)) {
    //         call_user_func_array([$this, $function], $arguments);
    //     }
    // }
    /**
     * Summary of getRoleName.
     *
     * @param int|null $id
     */
    public function getRoleNameForAuthenticatedUser(): string
    {
        return Auth::user()->role->name;
    }

    /**
     * Check If the Rolename in $roles array.
     */
    public function roleNameIn(array $roles): bool
    {
        return in_array($this->getRoleNameForAuthenticatedUser(), $roles);
    }

    /**
     * Get Role ID By Name.
     */
    public function getRolesIdsByName(array $roles): array
    {
        $res = [];
        // $allRoles = Role::whereIn('name', $roles)->get(['id']);
        $cachedRoles = $this->getCachedRoles();
        foreach ($cachedRoles as $roleId => $roleName) {
            foreach ($roles as $role) {
                if ($role == $roleName) $res[] = $roleId;
            }
        }
        return $res;
    }

    public function getRoleIdByName(string $name)
    {
        return Role::where('name', $name)->value('id');
    }

    public function roleIdIn(int $roleId, array $values): bool
    {
        return in_array(Role::where('id', $roleId)->value('name'), $values);
    }

    public function getRoleNameById(int $id)
    {
        $roles = $this->getCachedRoles();
        return $roles[$id] ?? null;
    }

    public function getCachedRoles()
    {
        if (!Cache::has('roles')) {
            $roles = [];
            foreach (Role::all(['id', 'name']) as $role) {
                $roles[$role->id] = $role->name;
            }
            Cache::set('roles', $roles);
        }
        return Cache::get('roles');
    }
}
