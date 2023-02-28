<?php

namespace App\Services\Api\V1\Auth;

use App\Http\Resources\Api\V1\Profile\ProfileResource;
use App\Models\User;
use App\Models\V1\Role;
use App\Traits\RoleTrait;
use App\Traits\StringTrait;
use App\Traits\Translatable;
use App\Traits\UserTrait;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    use StringTrait;
    use RoleTrait;
    use UserTrait;
    use Translatable;

    public function __construct(
        protected User $userModel
    ) {
    }

    /**
     * Make Account For New User.
     *
     * @param $request
     * @return User|string|array
     */
    public function signup($request): User|string|array
    {
        // Check If Username Or Phone Exists
        $exists = User::where('username', $request->username)->orWhere('phone', $request->phone)
            ->first(['username', 'phone']);
        if (!$exists) {
            $roleName = $this->getRoleNameById($request->role);


            if (in_array($roleName, config('roles.signup_roles'))) {
                // Valid Data
                return $this->userModel->create($request->validated() + ['role_id' => $request->role]);
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
    public function login($request, bool $isVisitor = false)
    {
        // Check if the user exists
        if ($token = Auth::attempt($request->validated() + ['status' => $this->isActive()])) {
            $user = Auth::user();
            $roleChecked = false;
            if ($isVisitor && $this->roleNameIn(['visitor'])) $roleChecked = true;
            if (!$isVisitor && !$this->roleNameIn(['visitor'])) $roleChecked = true;
            if ($roleChecked) {

                return [
                    'username' => $user->username,
                    'phone' => $user->phone,
                    'full_name' => $this->strLimit($user->full_name),
                    'role' => $this->getRoleNameById($user->role_id),
                    'token' => $token,
                    'avatar' => asset('/storage/users/' . ($user->avatar ?: 'user.png'))
                ];
            }
            // Logout Wrong User
            Auth::logout();
        }

        return null;
    }
}
