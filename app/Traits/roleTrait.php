<?php

namespace App\Traits;

use App\Models\V1\Role;
use Illuminate\Support\Facades\Auth;

trait roleTrait
{
    use userTrait;

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
        foreach ($roles as $roleName) {
            $res[] = Role::where('name', $roleName)->first(['id'])->id;
        }

        return $res;
    }
}
