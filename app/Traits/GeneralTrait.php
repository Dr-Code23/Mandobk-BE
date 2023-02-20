<?php

namespace App\Traits;

use App\Models\V1\Role;
use Cache;

trait GeneralTrait
{
    use RoleTrait;
    public function formatPatchNumber(string $val, int $roleId)
    {
        // Cache All Roles Ids
        if (!Cache::get('all_roles')) {
            $roles = [];
            foreach (Role::all(['id', 'name']) as $role) {
                $roles[$role->name] = $role->id;
            }
            Cache::set('all_roles', $roles);
        }
        $all_roles = Cache::get('all_roles');

        // Return The Original Path Number For All Users Except Admins

        if ($this->roleNameIn(['ceo', 'data_entry'])) {
            foreach ($all_roles as $role_name => $role_id) {
                if ($role_id == $roleId) {
                    return config('roles.role_patch_number_symbol.' . $role_name) . '-' . $val;
                }
            }
        }
        return $val;
    }
}
