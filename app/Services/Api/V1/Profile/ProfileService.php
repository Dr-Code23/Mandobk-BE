<?php

namespace App\Services\Api\V1\Profile;

use App\Models\User;
use App\Traits\FileOperationTrait;
use Auth;

class ProfileService
{
    use FileOperationTrait;
    public function changeUserInfo($request): bool
    {
        // Store avatar
        $data = $request->validated();
        $user = User::where('id', Auth::id());
        if ($request->has('avatar')) {
            // Delete The Old Avatar First
            if ($user->avatar != 'user.png') {
                $this->deleteImage('users/' . $user->avatar);
            }
            $imagePath = explode('/', $request->file('avatar')->store('public/users'))[2];
            $data->avatar = $imagePath;
        }
        $user->update($request->validated());
        return true;
    }
}
