<?php

namespace App\Traits;

use App\Http\Controllers\Api\V1\Roles\RoleController;
use App\Models\User;
use App\Models\V1\Role;
use App\Models\V1\SubUser;
use App\Models\V1\VisitorRecipe;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait UserTrait
{
    use HttpResponse;
    /**
     * Check If The User Has A Specific Permission.
     */
    public function hasPermission(string $permissionName = null, string $ExcludeCEO = 'yes'): bool
    {
        $roleController = new RoleController();
        $role_name=$roleController->getRoleNameById(auth()->user()->role_id);

        //! This Line Not Working
        // $role_name = $this->getRoleNameById(auth()->id());
        $permissions = [];
        if ($ExcludeCEO == 'no') {
            $permissions[] = 'ceo';
        }
        if ($permissionName) {
            $permissions[] = $permissionName;
            if(in_array($permissionName , ['pharmacy' , 'pharmacy_sub_user']))
                $permissions += ['pharmacy' , 'pharmacy_sub_user'];
        }

        return in_array($role_name, $permissions);
    }

    /**
     * Forbidden User To Access
     * @param string $excluded
     * @return bool
     */
    public function userHasNoPermissions(string $excluded): bool
    {
        $roleController = new RoleController();
        $role_name=$roleController->getRoleNameById(auth()->user()->role_id);
        return $role_name == $excluded;
    }

    /**
     * Get Logged User Information
     * @return Authenticatable|null
     */
    public function getAuthenticatedUserInformation(): ?\Illuminate\Contracts\Auth\Authenticatable
    {
        return auth()->user();
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
     * Get Sub Users For User Or For Logged User If $user_id = null
     *
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
     * Return Deleted Status
     *
     * @return string
     */
    public function isDeleted(): string
    {
        return '0';
    }

    /**
     * Return Frozen Status
     *
     * @return string
     */
    public function isFrozen(): string
    {
        return '2';
    }
}
