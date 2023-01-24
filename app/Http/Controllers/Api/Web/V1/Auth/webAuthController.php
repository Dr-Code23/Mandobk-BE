<?php

namespace App\Http\Controllers\Api\Web\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Web\V1\Auth\webLoginRequest as AuthWebLoginRequest;
use App\Http\Requests\Api\Web\V1\Auth\webSignUpRequest as AuthWebSignUpRequest;
use App\Models\User;
use App\Traits\HttpResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class webAuthController extends Controller
{
    use HttpResponse;

    /**
     * Register A New User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function signup(AuthWebSignUpRequest $req)
    {
        User::create([
            'full_name' => $req->full_name,
            'username' => $req->username,
            'password' => Hash::make($req->password),
            'phone' => $req->phone,
            'role' => $req->role,
        ]);
        $token = Auth::attempt(['username' => $req->username, 'password' => $req->password]);
        $jwt_cookie = cookie('jwt_token', $token, 60);

        return $this->responseWithCookie($jwt_cookie, null, 'Account Created Successfully , and user logged in');
    }

    /**
     * Login User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(AuthWebLoginRequest $req)
    {
        // Get Credentials
        $username = htmlspecialchars($req->username);
        $password = htmlspecialchars($req->password);

        // Check if the user exists
        if ($token = Auth::attempt(['username' => $username, 'password' => $password])) {
            $user = Auth::user();
            $jwt_cookie = cookie('jwt_token', $token, 60);

            return $this->responseWithCookie($jwt_cookie, [
                'username' => $user->username,
                'full_name' => $user->full_name,
                'role' => $user->role,
            ], 'User Authenticated Successfully');
        } else {
            return $this->unauthenticatedResponse('You are not authorized', Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Logout User
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        Auth::logout();

        return $this->success(msg: 'User Logged out successfully');
    }
}
