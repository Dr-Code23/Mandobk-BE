<?php

namespace App\Services\Api\V1\Site\SubUsers;

use App\Models\User;
use App\Models\V1\SubUser;
use App\Traits\RoleTrait;
use App\Traits\UserTrait;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SubUserService
{
    use UserTrait , RoleTrait;

    /**
     * Show All Sub Users
     */
    public function showAllSubUsers(): Collection
    {
        return SubUser::join('users', 'users.id', 'sub_users.sub_user_id')
            ->where('sub_users.parent_id', Auth::id())
            ->where('users.status', '1')
            ->get([
                'users.id as id',
                'users.full_name',
                'users.username as username',
                'users.created_at as created_at',
                'users.updated_at as updated_at',
            ]);
    }

    /**
     * Show One Sub User
     */
    public function showOneSubUser($subUser): User|null
    {
        if (
            SubUser::where('parent_id', Auth::id())
                ->where('sub_user_id', $subUser->id)
                ->value('id') && $subUser->status == $this->isActive()
        ) {
            return $subUser;
        }

        return null;
    }

    public function storeSubUser($request): User
    {
        // Create The User
        $user = User::create([
            'full_name' => $request->input('name'),
            'username' => $request->input('username'),
            'password' => $request->input('password'),
            'role_id' => $this->getRoleIdByName('pharmacy_sub_user'),
            'status' => '1',
        ]);

        // Add SubUser To Pharmacy
        SubUser::create(['parent_id' => Auth::id(), 'sub_user_id' => $user->id]);

        return $user;
    }

    public function updateSubUser($request, $subUser): User|null
    {
        if (SubUser::where('parent_id', auth()->id())->where('sub_user_id', $subUser->id)->value('id') && $subUser->status == '1') {
            // Create The User
            $anyChangeOccur = false;
            if ($request->name != $subUser->full_name) {
                $subUser->full_name = $request->name;
                $anyChangeOccur = true;
            }
            if ($subUser->username != $request->username) {
                $subUser->username = $request->username;
                $anyChangeOccur = true;
            }
            if ($request->password && ! Hash::check($request->password, $subUser->password)) {
                $subUser->password = $request->password;
                $anyChangeOccur = true;
            }
            if ($anyChangeOccur) {
                $subUser->update();
            }

            return $subUser;
        }

        return null;
    }

    /**
     * Delete Sub User
     */
    public function deleteSubUser($subUser): bool
    {
        if (SubUser::where('parent_id', auth()->id())->where('sub_user_id', $subUser->id)->value('id') && $subUser->status == '1') {
            $subUser->status = '0';
            $subUser->update();

            return true;
        }

        return false;
    }
}
