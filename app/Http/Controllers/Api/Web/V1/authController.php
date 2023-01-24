<?php

namespace App\Http\Controllers\Api\Web\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Web\V1\Auth\webLoginRequest as AuthWebLoginRequest;
use App\Http\Requests\Api\Web\V1\Auth\webSignUpRequest as AuthWebSignUpRequest;
use App\Http\Requests\Api\Web\V1\webSignUpRequest;
use App\Http\Requests\Api\Web\V1\webLoginRequest;
use App\Models\User;
use App\Traits\HttpResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class authController extends Controller
{
    use HttpResponse;

    /**
     * Register A New User
     * @param AuthWebSignUpRequest $req
     * @return \Illuminate\Http\JsonResponse
     */

    public function signup(AuthWebSignUpRequest $req)
    {
        User::create([
            'full_name' => $req->full_name,
            'username' => $req->username,
            'password'=> Hash::make($req->password),
            'phone' => $req->phone,
            'role' => $req->role
        ]);
        $token = Auth::attempt(['username' => $req->username , 'password' => $req->password]);
        $jwt_cookie = cookie('jwt_token' , $token , 60);
        return $this->responseWithCookie($jwt_cookie,null, 'Account Created Successfully , and user logged in');
    }

    public function login(AuthWebLoginRequest $req){

    }
}
