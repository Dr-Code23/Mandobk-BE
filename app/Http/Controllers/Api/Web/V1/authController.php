<?php

namespace App\Http\Controllers\Api\Web\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Web\V1\AuthRequest;
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
     * @param AuthRequest $req
     * @return \Illuminate\Http\JsonResponse
     */

    public function signup(AuthRequest $req)
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
}
