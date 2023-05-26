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
     */
    public function login(LoginRequest $request, AuthService $authService): JsonResponse
    {
        $response = $authService->login($request);

        $msg = __('messages.wrong_credentials');

        if (is_array($response)) {
            return $this->success($response, __('standard.logged_in'));
        }

        if ($response == 'frozen') {
            $msg = __('messages.detective');
        } elseif (in_array($response, ['deleted', 'wrong'])) {
            $msg = __('messages.wrong_credentials');
        }

        return $this->forbiddenResponse(
            $msg,
            code:Response::HTTP_UNAUTHORIZED
        );
    }

    /**
     * Register New User.
     */
    public function signup(SignupRequest $req, AuthService $authService): JsonResponse
    {
        $user = $authService->signup($req);

        if ($user instanceof User) {
            RegisterUserEvent::dispatch($user);

            return $this->createdResponse(null, __('standard.account_created'));
        }

        return $this->validationErrorsResponse($user);
    }

    /**
     * Logout User.
     */
    public function logout(): JsonResponse
    {
        Auth::logout();

        return $this->success(msg: __('standard.logged_out'));
    }
}
