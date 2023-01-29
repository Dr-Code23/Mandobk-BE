<?php

namespace App\Http\Controllers\Api\Web\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Web\V1\Auth\webSignUpRequest as AuthWebSignUpRequest;
use App\Http\Resources\Api\Web\V1\Translation\translationResource;
use App\Models\Api\Web\V1\Role;
use App\Models\User;
use App\Traits\HttpResponse;
use App\Traits\StringTrait;
use App\Traits\translationTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class webSignUpController extends Controller
{
    use HttpResponse;
    use translationTrait;
    use StringTrait;

    private string $lang_directory_name = 'Auth';

    /**
     * Return Translation Content.
     *
     * @return translationResource
     */
    public function index()
    {
        return $this->translateResource("{$this->lang_directory_name}/signupTranslationFile");
    }

    /**
     * Register A New User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function signup(AuthWebSignUpRequest $req)
    {
        $full_name = $this->sanitizeString($req->full_name);
        $username = $this->sanitizeString($req->username);
        $role_name = $this->sanitizeString($req->role);
        $role = Role::where('name', $role_name)->first(['id' , 'name']);
        if ($role && in_array($role->name, ['company', 'pharmacy', 'super_pharmacy', 'storehouse', 'doctor'])) {
            // Valid Data
            User::create([
                'full_name' => $full_name,
                'username' => $username,
                'password' => Hash::make($req->password),
                'phone' => $req->phone,
                'role_id' => $role->id,
            ]);
            $token = Auth::attempt(['username' => $req->username, 'password' => $req->password]);
            $jwt_cookie = cookie('jwt_token', $token, 60);

            return $this->responseWithCookie($jwt_cookie, ['full_name' => $this->strLimit($full_name), 'username' => $username, 'role' => $role->name, 'token' => \Illuminate\Support\Str::random(50)], __('standard.account_created'));
        }

        // Role Is not found
        return $this->validation_errors([
            'role' => __('standard.role_name').' '.__('standard.not_found'),
        ]);
    }
}
