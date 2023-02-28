<?php

namespace App\Traits;

use App\Http\Controllers\Api\V1\Roles\RoleController;
use App\Models\User;
use App\Models\V1\Role;
use App\Models\V1\SubUser;
use App\Models\V1\VisitorRecipe;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait UserTrait
{
    use HttpResponse;
    /**
     * Check If The User Has A Specefic Permission.
     */
    public function hasPermission(string $permissionName = null, bool $ExcludeCEO = false): bool
    {
        $roleController = new RoleController();
        $role_name=$roleController->getRoleNameById(auth()->user()->role_id);
//        $role_name = Role::where('id', $this->getAuthenticatedUserInformation()->role_id)
//            ->first(['name'])->name;
        // $role_name = $this->getRoleNameById(auth()->id());
        $permissions = [];
        if (!$ExcludeCEO) {
            $permissions[] = 'ceo';
        }
        if ($permissionName) {
            $permissions[] = $permissionName;
            if(in_array($permissionName , ['pharmacy' , 'pharmacy_sub_user']))
                $permissions += ['pharmacy' , 'pharmacy_sub_user'];
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
            User::where('role_id', Role::where('name', $role)->value('id'))->get(['id', 'full_name'])
        );
    }


    /**
     * Get Sub Users For User
     * @param int|null $user_id
     * @return array
     */
    public function getSubUsersForUser(int $user_id = null): array
    {
        // If Authenticated User is a subUser and disabled , don't let him access data
        // But if a parent user want to access his data let him access
        $subUsers = [];
        $userRoleName = $this->getRoleNameForUser($user_id);
        $userId = $user_id ?: auth()->id();
        if(in_array($userRoleName , config('roles.rolesHasSubUsers'))){
            // Then it's Parent User
            foreach(SubUser::where('parent_id' , $userId)->get(['sub_user_id as id']) as $subUser){
                $subUsers[] = $subUser->id;
            }
            $subUsers[] = $userId;
        }
        else {

            $userSubUsers = [];
            // Want To Turn This into Eloquent
            if($userRoleName == 'pharmacy_sub_user'){
            $userSubUsers = DB::select(
                'SELECT sub_user_id,parent_id FROM sub_users WHERE parent_id = (SELECT parent_id FROM sub_users WHERE sub_user_id =?)',
                [$userId]
            );}
            if($userSubUsers){
                $parentIdSet = false;
                foreach($userSubUsers as $subUser){
                    $subUsers[] = $subUser->sub_user_id;
                    if(!$parentIdSet) {
                        $subUsers[] = $subUser->parent_id;
                        $parentIdSet = true;
                    }
                }
            }
            else $subUsers[] = $userId;
        }
//        return SubUser::where(function($query){
//                $query->select('parent_id')
//                    ->from(with(new SubUser)->getTable())
//                    ->where('sub_user_id' , 10);
//        })->get();
        return $subUsers;
    }

    /**
     * Summary of generateRandomNumberForVisitor.
     *
     * @return int
     */
    public function generateRandomNumberForVisitor(): int
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

    public function isFrozen(): string
    {
        return '2';
    }
}
