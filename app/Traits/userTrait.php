<?php

namespace App\Traits;

use App\Models\Api\V1\Role;
use App\Models\Api\V1\SubUser;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

trait userTrait
{
    use HttpResponse;

    /**
     * Check If The User Has A Specefic Permission.
     */
    public function hasPermission(string $permissionName = null, bool $ExcludeCEO = false): bool
    {
        $role_name = Role::where('id', $this->getAuthenticatedUserInformation()->role_id)->first(['name'])->name;

        $permissions = [];
        if (!$ExcludeCEO) {
            $permissions[] = 'ceo';
        }
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

    public function getUserSelectBox(string $role)
    {
        return $this->resourceResponse(User::where('role_id', Role::where('name', $role)->first(['name']))->get(['id', 'full_name']));
    }

    public function getSubUsersForAuthenticatedUser(int $user_id = null)
    {
        $user_id = $user_id ?? $this->getAuthenticatedUserId();
        $subusers = [];

        // Check If the user is a subuser
        $parent_id = SubUser::where('sub_user_id', $user_id)->first(['parent_id as id']);
        if ($parent_id) {
            // Then authenticated user is a sub user , so get all sub_users of a parent user
            foreach (SubUser::where('parent_id', $parent_id->id)->get(['sub_user_id as id']) as $subuser) {
                $subusers[] = $subuser->id;
            }
        } else {
            foreach (SubUser::where('parent_id', $user_id)->get(['sub_user_id as id']) as $subuser) {
                $subusers[] = $subuser->id;
            }
        }
        $subusers[] = ($parent_id ? $parent_id->id : $user_id);

        return $subusers;
    }
}
