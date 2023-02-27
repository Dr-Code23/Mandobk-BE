<?php

namespace App\Services\Api\V1\Auth;

use App\Models\User;
use App\Models\V1\Role;
use App\Traits\RoleTrait;
use App\Traits\StringTrait;
use App\Traits\UserTrait;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    use StringTrait, RoleTrait, UserTrait;

    /**
     * @var User
     */
    protected User $userModel;

    /**
     * @var Role
     */
    protected Role $roleModel;

    /**
     * @param User $user
     * @param Role $role
     */
    public function __construct(User $user, Role $role){
        $this->userModel = $user;
        $this->roleModel = $role;
    }

    /**
     * Make Account For New User.
     *
     * @param $request
     * @return bool
     */
    public function signup($request): bool
    {
        $roleName = $this->roleModel->query()
            ->where('id', $request->role)->value('name');

        // return $request->validated();
        if (in_array($roleName, config('roles.signup_roles'))) {
            // Valid Data
            $this->userModel->create($request->validated() + ['role_id' => $request->role]);
            return true;
        }

        // Role Is not found
        return false;
    }

    /**
     * Login User
     *
     * @param $request
     * @param boolean $isVisitor
     * @return array|null
     */
    public function login($request, bool $isVisitor = false): ?array
    {
        // Check if the user exists
        if ($token = Auth::attempt($request->validated() + ['status' => $this->isActive()])) {
            $user = Auth::user();
            $roleChecked = false;
            if ($isVisitor && $this->roleNameIn(['visitor'])) $roleChecked = true;
            if (!$isVisitor && !$this->roleNameIn(['visitor'])) $roleChecked = true;
            if ($roleChecked) {

// make this in resource

                return [
                    'username' => $user->username,
                    'phone' => $user->phone,
                    'full_name' => $this->strLimit($user->full_name),
                    'role' => Role::where('id', $user->role_id)->value('name'),
                    'token' => $token,
                    'avatar' => asset('/storage/users/' . ($user->avatar ? $user->avatar : 'user.png'))
                ];
            }
            // Logout Wrong User
            Auth::logout();
        }

        return null;
    }
}
