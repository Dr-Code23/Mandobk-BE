<?php

namespace App\Services\Api\V1\Auth;

use App\Models\User;
use App\Models\V1\Role;
use App\Traits\RoleTrait;
use App\Traits\StringTrait;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    use StringTrait;
    use RoleTrait;

    public function __construct(
        protected User $userModel
    ) {
    }

    /**
     * Make Account For New User.
     *
     * @param $request
     * @return array|null
     */
    public function signup($request): array|null
    {
        $roleName = Role::where('id', $request->role)->value('name');

        // return $request->validated();
        if (in_array($roleName, config('roles.signup_roles'))) {
            // Valid Data
            $this->userModel->create($request->validated() + ['role_id' => $request->role]);
            $token = Auth::attempt([
                'username' => $request->username,
                'password' => $request->password,
            ]);

            return [
                'full_name' => $this->strLimit($request->full_name),
                'username' => $request->username,
                'role' => $roleName,
                'token' => $token,
            ];
        }

        // Role Is not found
        return null;
    }

    /**
     * Login User
     *
     * @param $request
     * @param boolean $isVisitor
     * @return array|null
     */
    public function login($request, bool $isVisitor = false): array|null
    {
        // Check if the user exists
        if ($token = Auth::attempt($request->validated() + ['status' => '1'])) {
            $user = Auth::user();
            $roleChecked = false;
            if ($isVisitor && $this->roleNameIn(['visitor'])) $roleChecked = true;
            if (!$isVisitor && !$this->roleNameIn(['visitor'])) $roleChecked = true;
            if ($roleChecked) {
                return [
                    'username' => $user->username,
                    'phone' => $user->phone,
                    'full_name' => $this->strLimit($user->full_name),
                    'role' => Role::where('id', $user->role_id)->value('name'),
                    'token' => $token,
                ];
            }
            // Logout Wrong User
            Auth::logout();
        }

        return null;
    }
}
