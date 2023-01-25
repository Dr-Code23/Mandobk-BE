<?php

namespace App\Http\Controllers\Api\Web\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Web\V1\Auth\webLoginRequest as AuthWebLoginRequest;
use App\Http\Resources\Api\Web\V1\Translation\translationResource;
use App\Traits\fileOperationTrait;
use App\Traits\HttpResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class webLoginController extends Controller
{
    use HttpResponse;
    use fileOperationTrait;
    private string $lang_directory_name = 'Auth';

    /**
     * Return Translation Content.
     *
     * @return translationResource
     */
    public function index()
    {
        // Get The Translation File
        $login_view = $this->getWebTranslationFile("{$this->lang_directory_name}/loginTranslationFile.php");

        return new translationResource($login_view);
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
                'token' => \Illuminate\Support\Str::random(50),
            ], 'User Authenticated Successfully');
        } else {
            return $this->unauthenticatedResponse('You are not authorized', Response::HTTP_UNAUTHORIZED);
        }
    }
}
