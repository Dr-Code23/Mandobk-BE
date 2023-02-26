<?php

namespace App\Traits;

use App\Models\V1\Role;
use Illuminate\Support\Facades\Auth;

trait RoleTrait
{
    use UserTrait;

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
        $allRoles = Role::whereIn('name', $roles)->get(['id']);
        foreach ($allRoles as $roleName) {
            $res[] = $roleName->id;
        }

        return $res;
    }

    public function getRoleIdByName(string $name)
    {
        return Role::where('name', $name)->value('id');
    }
}
