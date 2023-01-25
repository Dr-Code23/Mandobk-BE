<?php

namespace App\Http\Controllers\Api\Web\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Web\V1\Auth\webSignUpRequest as AuthWebSignUpRequest;
use App\Http\Resources\Api\Web\V1\Translation\translationResource;
use App\Models\User;
use App\Traits\fileOperationTrait;
use App\Traits\HttpResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class webSignUpController extends Controller
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
        $login_view = $this->getWebTranslationFile("{$this->lang_directory_name}/signupTranslationFile.php");

        return new translationResource($login_view);
    }

    /**
     * Register A New User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function signup(AuthWebSignUpRequest $req)
    {
        User::create([
            'full_name' => $req->full_name,
            'username' => $req->username,
            'password' => Hash::make($req->password),
            'phone' => $req->phone,
            'role' => $req->role,
        ]);
        $token = Auth::attempt(['username' => $req->username, 'password' => $req->password]);
        $jwt_cookie = cookie('jwt_token', $token, 60);

        return $this->responseWithCookie($jwt_cookie, ['token' => \Illuminate\Support\Str::random(50)], 'Account Created Successfully , and user logged in');
    }
}
