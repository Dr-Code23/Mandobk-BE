<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Events\RegisterUserEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Http\Requests\Api\V1\Auth\signUpRequest;
use App\Models\User;
use App\Services\Api\V1\Auth\AuthService;
use App\Traits\HttpResponse;
use App\Traits\Translatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    use HttpResponse, Translatable;

    /**
     * Login User.
     *
     * @param LoginRequest $request
     * @param AuthService $authService
     * @return JsonResponse|mixed
     */
    public function login(LoginRequest $request, AuthService $authService): mixed
    {
        $user = $authService->login($request, false);

        if ($user)
            return $this->success($user, __('standard.logged_in'));

        return $this->forbiddenResponse(
            __('standard.not_authorized'),
            null,
            Response::HTTP_UNAUTHORIZED
        );
    }

    /**
     * Register New User.
     *
     * @param signUpRequest $req
     * @param AuthService $authService
     * @return JsonResponse
     */
    public function signup(signUpRequest $req, AuthService $authService): JsonResponse
    {
        $added = $authService->signup($req);
        $user = User::where('username', $req->username)->first();
        if ($added) {
            RegisterUserEvent::dispatch($user);
            return $this->createdResponse(null, __('standard.account_created'));
        }

        return $this->validation_errors([
            'role' => __('standard.role_name') . ' ' . __('standard.not_found'),
        ]);
    }

    /**
     * Logout User.
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        Auth::logout();

        return $this->success(msg: __('standard.logged_out'));
    }
}
