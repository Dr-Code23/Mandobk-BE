<?php

namespace App\Services\Api\V1\Auth;

use App\Models\User;
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
     */
    public function signup($request): User|string|array
    {
        // Check If Username Or Phone Exists
        $exists = User::where(
            'username',
            $request->username
        )->orWhere('phone', $request->phone)
            ->first(['username', 'phone']);
        if (! $exists) {
            $roleName = $this->getRoleNameById($request->role);

            if (in_array($roleName, config('roles.signup_roles'))) {
                // Valid Data
                return $this->userModel->create($request->validated() + ['role_id' => $request->role]);
            }

            $error['role'][] = $this->translateErrorMessage('role', 'not_found');
        } else {
            if ($request->username == $exists->username) {
                $error['username'][] = $this->translateErrorMessage('username', 'exists');
            } else {
                $error['phone'][] = $this->translateErrorMessage('phone', 'exists');
            }
        }

        return $error;
    }

    /**
     * Login User
     *
     * @return array|null|bool
     */
    public function login($request, bool $isVisitor = false): string|array
    {
        // Check if the user exists
        if ($token = Auth::attempt($request->validated())) {
            $user = Auth::user();

            // Frozen User
            if ($user->status == '2') {
                auth()->logout();

                return 'frozen';
            }

            // Delete User
            if ($user->status == '0') {
                return 'deleted';
            }
            $roleChecked = false;

            if (
                ($isVisitor && $this->roleNameIn(['visitor']))
                || (! $isVisitor && ! $this->roleNameIn(['visitor']))
            ) {
                $roleChecked = true;
            }
            if ($roleChecked) {
                return [
                    'username' => $user->username,
                    'phone' => $user->phone,
                    'full_name' => $this->strLimit($user->full_name),
                    'role' => $this->getRoleNameById($user->role_id),
                    'token' => $token,
                    'avatar' => asset('/storage/users/'.($user->avatar ?: 'user.png')),
                ];
            }
        }

        return 'wrong';
    }
}
