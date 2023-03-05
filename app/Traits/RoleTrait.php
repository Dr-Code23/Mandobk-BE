<?php

namespace App\Traits;

use App\Models\User;
use App\Models\V1\Role;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

trait RoleTrait
{
    use UserTrait;
    /**
     * Summary of getRoleName.
     */

    public function getRoleNameForAuthenticatedUser(): string
    {
        $cachedRoles = $this->getCachedRoles();
        return $cachedRoles[auth()->user()->role_id];
    }

    /**
     * Check If the Role name in $roles array.
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
        $cachedRoles = $this->getCachedRoles();
        foreach ($cachedRoles as $roleId => $roleName) {
            foreach ($roles as $role) {
                if ($role == $roleName) $res[] = $roleId;
            }
        }
        return $res;
    }

    /**
     * Get Role Ids By Name
     * @param string $name
     * @return int
     */
    public function getRoleIdByName(string $name): int
    {
        $roles = $this->getCachedRoles();
        foreach ($roles as $id => $roleName) {
            if ($name == $roleName) return $id;
        }
        // Customer That Has No Usage
        return 13;
    }

    public function roleIdIn(int $roleId, array $values): bool
    {
        return in_array(Role::where('id', $roleId)->value('name'), $values);
    }

    /**
     * Get Role Name By ID
     * @param int $id
     * @return string|null
     */
    public function getRoleNameById(int $id): string|null
    {
        $roles = $this->getCachedRoles();
        return $roles[$id] ?? null;
    }

    /**
     * Get Role Details From Array By Name
     * @param array $roles
     * @return Collection
     */
    public function getRoleDetailsFromArrayByName(array $roles): Collection
    {
        $allRoles = $this->getCachedRoles();
        $res = [];
        $cnt = 0;
        foreach ($roles as $wantedRole) {
            foreach ($allRoles as $id => $name) {
                if ($wantedRole == $name) {
                    $res[$cnt]['id'] = $id;
                    $res[$cnt]['name'] = $name;
                }
            }
            $cnt++;
        }

        return new Collection($res);
    }

    /**
     * Get Role Name For User
     * @param $userId
     * @return string
     */
    public function getRoleNameForUser($userId = null): string
    {
        if (!$userId) {
            $roleName = $this->getRoleNameForAuthenticatedUser();
        }
        else {
            $cachedRoles = $this->getCachedRoles();
            $roleName = $cachedRoles[
                User::where('id' , $userId)
                    ->value('role_id')
            ];
        }
        return $roleName;
    }

    /**
     * Get Roles From Cache
     * @return array
     */
    public function getCachedRoles(): array
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
