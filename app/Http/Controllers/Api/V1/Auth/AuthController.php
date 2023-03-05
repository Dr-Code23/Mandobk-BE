<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Events\RegisterUserEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Http\Requests\Api\V1\Auth\SignupRequest;
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
     * @return JsonResponse
     */
    public function login(LoginRequest $request, AuthService $authService): JsonResponse
    {
        $user = $authService->login($request);

        $msg = __('standard.not_authorized');
        if (is_bool($user) && !$user) {
            $msg = 'Your Account Is Detactive , Contanct With Admin';
            $msg = __('standard.detactive');
        }
        if (is_array($user)) {
            return $this->success($user, __('standard.logged_in'));
        }

        return $this->forbiddenResponse(
            $msg,
            null,
            Response::HTTP_UNAUTHORIZED
        );
    }

    /**
     * Register New User.
     *
     * @param SignupRequest $req
     * @param AuthService $authService
     * @return JsonResponse
     */
    public function signup(SignupRequest $req, AuthService $authService): JsonResponse
    {
        $user = $authService->signup($req);

        if ($user instanceof User) {
            RegisterUserEvent::dispatch($user);

            return $this->createdResponse(null, __('standard.account_created'));
        }

        return $this->validation_errors($user);
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
