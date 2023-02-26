<?php

namespace App\Traits;

use App\Models\User;
use App\Models\V1\Role;
use App\Models\V1\SubUser;
use App\Models\V1\VisitorRecipe;
use Illuminate\Support\Facades\Auth;

trait UserTrait
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
        return Auth::id();
    }

    public function getUserSelectBox(string $role)
    {
        return $this->resourceResponse(
            User::where('role_id', Role::where('name', $role)->first(['name']))->get(['id', 'full_name'])
        );
    }

    public function getSubUsersForAuthenticatedUser(int $user_id = null)
    {
        // If Authenticated User is a subuser and disabled , don't let him access data
        // But if a parent user want to access his data let him access
        $user_id = $user_id ?: Auth::id();
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

    /**
     * Summary of generateRandomNumberForVisitor.
     *
     * @return string
     */
    public function generateRandomNumberForVisitor()
    {
        $random_number = VisitorRecipe::orderByDesc('id')->first(['random_number as number']);

        return $random_number ? ($random_number->number + 1) : 1;
    }

    /**
     * Determine if user is active or not
     *
     * @return string
     */
    public function isActive(): string
    {
        return '1';
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    public function isDeleted(): string
    {
        return '0';
    }

    public function isFrozen()
    {
        return '2';
    }
}
