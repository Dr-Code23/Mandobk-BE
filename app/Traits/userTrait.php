<?php

namespace App\Traits;

use App\Models\Api\Web\V1\Role;
use Illuminate\Support\Facades\Auth;

trait userTrait
{
    /**
     * Check If The User Has A Specefic Permission.
     */
    public function hasPermission(string $permissionName = null): bool
    {
        $role_name = Role::where('id', Auth::user()->role_id)->first(['name'])->name;
        $permissions = ['ceo'];
        if ($permissionName) {
            $permissions[] = $permissionName;
        }

        return in_array($role_name, $permissions);
    }

    public function getAuthenticatedUserInformation()
    {
        return Auth::user();
    }

    public function getAuthenticatedUserId()
    {
        return Auth::user()->id;
    }
}
