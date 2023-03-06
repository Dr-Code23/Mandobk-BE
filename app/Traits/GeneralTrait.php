<?php

namespace App\Traits;

use App\Models\V1\Role;
use Illuminate\Support\Facades\Cache;

trait GeneralTrait
{
    use RoleTrait;
    public function formatPatchNumber(string $val, int $roleId)
    {
        // Cache All Roles Ids
        // if (!Cache::get('all_roles')) {
        //     $roles = [];
        //     foreach (Role::all(['id', 'name']) as $role) {
        //         $roles[$role->name] = $role->id;
        //     }
        //     Cache::set('all_roles', $roles);
        // }
        $all_roles = $this->getCachedRoles();

        // Return The Original Path Number For All Users Except Admins

        if ($this->roleNameIn(['ceo', 'data_entry'])) {
            foreach ($all_roles as $loopRoleId => $loopRoleName) {
                if ($loopRoleId == $roleId) {
                    return config('roles.role_patch_number_symbol.' . $loopRoleName) . '-' . $val;
                }
            }
        }
        return $val;
    }
}
