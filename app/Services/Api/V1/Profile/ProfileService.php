<?php

namespace App\Services\Api\V1\Profile;

use App\Models\User;
use App\Traits\FileOperationTrait;
use Auth;
use Hash;

class ProfileService
{
    use FileOperationTrait;
    public function changeUserInfo($request)
    {
        // Store avatar
        $data = $request->validated();
        $user = Auth::user();
        $anyChangeOccur = false;
        $passowrdChanged = false;
        if ($request->has('avatar')) {
            // Delete The Old Avatar First
            if ($user->avatar) {
                $this->deleteImage('users/' . $user->avatar);
            }
            $imagePath = explode('/', $request->file('avatar')->store('public/users'))[2];
            $user->avatar = $imagePath;
            $anyChangeOccur = true;
        }
        if ($user->full_name != $data['full_name']) {
            $user->full_name = $data['full_name'];
            $anyChangeOccur = true;
        }
        if ($user->phone != $data['phone']) {
            $user->phone = $data['phone'];
            $anyChangeOccur = true;
        }
        if ($request->has('password')) {
            if (!Hash::check($data['password'], $user->password)) {
                $user->password = $data['password'];
                $anyChangeOccur = true;
                $passowrdChanged = true;
            }
        }
        if ($anyChangeOccur)
            $user->update();
        if ($passowrdChanged) Auth::logout();
        return $user;
    }
}
