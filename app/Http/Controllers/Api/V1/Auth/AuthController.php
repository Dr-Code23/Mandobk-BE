<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Http\Requests\Api\V1\Auth\signUpRequest;
use App\Models\User;
use App\Models\V1\Role;
use App\Traits\HttpResponse;
use App\Traits\StringTrait;
use App\Traits\translationTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    use HttpResponse;
    use translationTrait;
    use StringTrait;

    protected User $userModel;

    public function __construct(User $user)
    {
        $this->userModel = $user;
    }

    /**
     * Login User.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        // Get Credentials
        $username = $this->sanitizeString($request->post('username'));

        $password = $request->post('password');

        // Check if the user exists
        if ($token = Auth::attempt(['username' => $username, 'password' => $password, 'status' => '1'])) {
            $user = Auth::user();

            return $this->success([
                'username' => $user->username,
                'full_name' => $this->strLimit($user->full_name),
                'role' => Role::where('id', $user->role_id)->first('name')->name ?? null,
                'token' => $token,
            ]);
        } else {
            return $this->forbiddenResponse(__('standard.not_authorized'), null, Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Register A New User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function signup(signUpRequest $req)
    {
        $full_name = $this->sanitizeString($req->post('full_name'));
        $username = $this->sanitizeString($req->post('username'));
        $role_id = $this->sanitizeString($req->post('role'));
        $role = Role::where('id', $role_id)->first(['id', 'name']);
        if ($role && in_array($role->name, config('roles.signup_roles'))) {
            // Valid Data
            $this->userModel->create([
                'full_name' => $full_name,
                'username' => $username,
                'password' => Hash::make($req->password),
                'phone' => $req->phone,
                'role_id' => $role->id,
            ]);
            $token = Auth::attempt(['username' => $req->username, 'password' => $req->password]);
            // $jwt_cookie = cookie('jwt_token', $token, 1e9);

            return $this->success([
                'full_name' => $this->strLimit($full_name),
                'username' => $username,
                'role' => $role->name,
                'token' => $token,
            ],
                __('standard.account_created')
            );
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
