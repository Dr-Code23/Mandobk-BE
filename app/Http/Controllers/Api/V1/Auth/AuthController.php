<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Web\V1\Auth\webLoginRequest;
use App\Http\Requests\Api\Web\V1\Auth\webSignUpRequest;
use App\Models\Api\Web\V1\Role;
use App\Models\User;
use App\Traits\HttpResponse;
use App\Traits\StringTrait;
use App\Traits\translationTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    use HttpResponse;
    use translationTrait;
    use StringTrait;

    /**
     * Login User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(webLoginRequest $request)
    {
        // Get Credentials
        $username = $this->sanitizeString($request->username);
        $password = $request->password;

        // Check if the user exists
        if ($token = Auth::attempt(['username' => $username, 'password' => $password])) {
            $user = Auth::user();
            $jwt_cookie = cookie('jwt_token', $token, 60);

            return $this->responseWithCookie($jwt_cookie, [
                'username' => $user->username,
                'full_name' => $this->strLimit($user->full_name),
                'role' => Role::where('id', $user->role_id)->first('name')->name,
                'token' => \Illuminate\Support\Str::random(50),
            ], __('standard.logged_in'));
        } else {
            return $this->forbiddenResponse(__('standard.not_authorized'), null, Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Register A New User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function signup(webSignUpRequest $req)
    {
        $full_name = $this->sanitizeString($req->full_name);
        $username = $this->sanitizeString($req->username);
        $role_id = $this->sanitizeString($req->role);
        $role = Role::where('id', $role_id)->first(['id', 'name']);
        if ($role && in_array($role->name, config('roles.signup_roles'))) {
            // Valid Data
            User::create([
                'full_name' => $full_name,
                'username' => $username,
                'password' => Hash::make($req->password),
                'phone' => $req->phone,
                'role_id' => $role->id,
            ]);
            $token = Auth::attempt(['username' => $req->username, 'password' => $req->password]);
            $jwt_cookie = cookie('jwt_token', $token, 1e9);

            return $this->responseWithCookie($jwt_cookie, ['full_name' => $this->strLimit($full_name), 'username' => $username, 'role' => $role->name, 'token' => \Illuminate\Support\Str::random(50)], __('standard.account_created'));
        }

        // Role Is not found
        return $this->validation_errors([
            'role' => __('standard.role_name').' '.__('standard.not_found'),
        ]);
    }

    /**
     * Logout User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        Auth::logout();

        return $this->success(msg: __('standard.logged_out'));
    }
}
