<?php

namespace App\Http\Controllers\Api\Web\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Web\V1\Auth\webLoginRequest as AuthWebLoginRequest;
use App\Http\Resources\Api\Web\V1\Translation\translationResource;
use App\Models\Api\Web\V1\Role;
use App\Traits\HttpResponse;
use App\Traits\StringTrait;
use App\Traits\translationTrait;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class webLoginController extends Controller
{
    use HttpResponse;
    use translationTrait;
    use StringTrait;
    private string $lang_directory_name = 'Auth';

    /**
     * Return Login Translation Content.
     *
     * @return translationResource
     */
    public function index()
    {
        return $this->translateResource("{$this->lang_directory_name}/loginTranslationFile");
    }

    /**
     * Login User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(AuthWebLoginRequest $req)
    {
        // Get Credentials
        $username = $this->sanitizeString($req->username);
        $password = $req->password;

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
}
