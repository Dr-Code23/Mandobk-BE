<?php

namespace App\Http\Controllers\Api\V1\Mobile\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Http\Requests\Api\V1\Auth\SignupRequest;
use App\Services\Api\V1\Auth\AuthService;
use App\Traits\HttpResponse;
use App\Traits\Translatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class MobileAuthController extends Controller
{
    use HttpResponse, Translatable;

    /**
     * Login User
     *
     * @param LoginRequest $request
     * @param AuthService $authService
     * @return JsonResponse
     */
    public function login(LoginRequest $request, AuthService $authService): JsonResponse
    {
        $user = $authService->login($request, true);

        if ($user)
            return $this->success($user, __('standard.logged_in'));

        return $this->forbiddenResponse(
            __('standard.not_authorized'),
            null,
            ResponseAlias::HTTP_UNAUTHORIZED
        );
    }

    /**
     * Register New User
     *
     * @param SignupRequest $req
     * @param AuthService $authService
     * @return JsonResponse
     */
    public function signup(SignupRequest $req, AuthService $authService): JsonResponse
    {
        $user = $authService->signup($req);
        if ($user) {
            return $this->success($user, __('standard.account_created'));
        }

        return $this->validation_errors([
            'role' => __('standard.role_name') . ' ' . __('standard.not_found'),
        ]);
    }

    /**
     * Logout User
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        Auth::logout();

        return $this->success(msg: __('standard.logged_out'));
    }
}
