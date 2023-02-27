<?php

namespace App\Services\Api\V1\Auth;

use App\Models\User;
use App\Models\V1\Role;
use App\Traits\RoleTrait;
use App\Traits\StringTrait;
use App\Traits\Translatable;
use App\Traits\UserTrait;
use Illuminate\Support\Facades\Auth;

class AuthService
{
<<<<<<< HEAD
    use StringTrait, RoleTrait, UserTrait;
=======
    use StringTrait;
    use RoleTrait;
    use UserTrait;
    use Translatable;
>>>>>>> abb8fbeb550debcbc4e19c3bb7f619b8dd70ab0e

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
     * @return User|string|array
     */
    public function signup($request): User|string|array
    {
<<<<<<< HEAD
        $roleName = $this->roleModel->query()
            ->where('id', $request->role)->value('name');
=======
>>>>>>> abb8fbeb550debcbc4e19c3bb7f619b8dd70ab0e

        // Check If Username Or Phone Exists
        $exists = User::where('username', $request->username)->orWhere('phone', $request->phone)
            ->first(['username', 'phone']);
        if (!$exists) {
            $roleName = $this->getRoleNameById($request->role);

            if (in_array($roleName, config('roles.signup_roles'))) {
                // Valid Data
                $user = $this->userModel->create($request->validated() + ['role_id' => $request->role]);
                return $user;
            }

            return  $this->translateErrorMessage('role', 'not_found');
        } else {
            if ($request->username == $exists->username) $error['username'][] = $this->translateErrorMessage('username', 'exists');
            else $error['phone'][]  = $this->translateErrorMessage('phone', 'exists');
        }
        return $error;
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
