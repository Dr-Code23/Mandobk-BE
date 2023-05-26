<?php

namespace App\Services\Api\V1\Dashboard;

use App\Models\User;
use App\Models\V1\Role;
use App\Traits\RoleTrait;
use App\Traits\Translatable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class MonitorAndEvaluationService
{
    use RoleTrait;
    use Translatable;

    /**
     * Show All Users
     */
    public function index(): Collection
    {
        return User::join('roles', 'roles.id', 'users.role_id')
            ->whereIn('roles.name', config('roles.monitor_roles'))
            ->where('users.id', '!=', Auth::id())
            ->where('status', $this->isActive())
            ->select(
                [
                    'users.id',
                    'users.full_name',
                    'users.username',
                    'users.role_id',
                    'roles.name as role_name',
                    'users.created_at',
                    'users.updated_at',
                ]
            )
            ->get();
    }

    /**
     * Show One User For Monitor
     */
    public function show($user): User|array
    {
        $errors = [];
        if ($user->id != Auth::id() && $user->status == $this->isActive()) {
            if (
                $role = Role::where('id', $user->role_id)
                ->whereIn('name', config('roles.monitor_roles'))->first(['name'])
            ) {
                $user->role_name = $role->name;

                return $user;
            }

            $errors['role'][] = $this->translateErrorMessage('role', 'not_found');
        } else {
            $errors['user'][] = $this->translateErrorMessage('user', 'not_found');
        }

        return $errors;
    }

    /**
     * Store User
     */
    public function store($request): User|array
    {
        // Check if role belong to monitor
        if ($role = Role::where('id', $request->role)
            ->whereIn('name', config('roles.monitor_roles'))
            ->first(['name'])
        ) {
            // Store Data
            $user = User::create(
                $request->validated() +
                ['status' => '1', 'role_id' => $request->role]
            );
            $user->role_name = $role->name;

            return $user;
        }

        $errors['role'][] = $this->translateErrorMessage('role', 'not_found');

        return $errors;
    }

    /**
     * Update User
     */
    public function update($request, $user): User|string|array
    {
        if ($user->id != Auth::id() && $user->status == $this->isActive()) {
            $role = Role::where('id', $request->role)
                ->whereIn('name', config('roles.monitor_roles'))
                ->first(['name as role_name', 'id']);

            if ($role) {
                $full_name = $this->sanitizeString($request->full_name);
                $username = $this->sanitizeString($request->username);

                $anyChangeOccurred = false;
                if ($user->full_name != $full_name) {
                    $user->full_name = $full_name;
                    $anyChangeOccurred = true;
                }
                if ($user->username != $username) {
                    $user->username = $username;
                    $anyChangeOccurred = true;
                }
                if ($request->has('password')) {
                    if (! Hash::check($request->password, $user->password)) {
                        $user->password = $request->password;
                        $anyChangeOccurred = true;
                    }
                }
                if ($user->role_id != $request->role) {
                    $user->role_id = $request->role;
                    $anyChangeOccurred = true;
                }
                if ($anyChangeOccurred) {
                    $user->update();
                    $user->role_name = $role->role_name;
                }

                return $user;
            }

            $error['role'][] = $this->translateErrorMessage('role', 'not_found');
        } else {
            $error = $this->translateErrorMessage('user', 'not_found');
        }

        return $error;
    }

    /**
     * Destroy User
     */
    public function destroy($user): bool|string
    {
        if ($user->id != Auth::id() && $user->status == $this->isActive()) {
            $user->load('role');
            if (in_array($user->role->name, config('roles.monitor_roles'))) {
                $user->status = $this->isDeleted();
                $user->save();

                return true;
            }
            $error = $this->translateErrorMessage('role', 'not_found');
        } else {
            $error = $this->translateErrorMessage('user', 'not_found');
        }

        return $error;
    }
}
