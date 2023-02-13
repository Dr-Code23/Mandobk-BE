<?php

namespace App\Http\Controllers\Api\V1\Users;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Site\Doctor\VisitorAccount\VisitorAccountResource;
use App\Models\User;
use App\Models\V1\Role;
use App\Models\V1\VisitorRecipe;
use App\Traits\HttpResponse;
use App\Traits\StringTrait;
use App\Traits\TranslationTrait;
use App\Traits\UserTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password as RulesPassword;

class UserController extends Controller
{
    use UserTrait;
    use TranslationTrait;
    use HttpResponse;
    use StringTrait;

    public function getUsersForSelectBox(Request $request)
    {
        $role_name = '';
        if ($request->routeIs('roles-storehouse-all')) {
            $role_name = 'storehouse';
        } elseif ($request->routeIs('roles-pharmacy-all')) {
            $role_name = 'pharmacy';
        }
        if ($role_name) {
            $role_id = Role::where('name', $role_name)->value('id');

            $users = User::where('role_id', $role_id)->get(['id', 'full_name']);

            return $this->resourceResponse($users);
        }

        return $this->notFoundResponse();
    }

    public function registerNewVisitor(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['bail', 'required', 'not_regex:'.config('regex.not_fully_numbers_symbols'), 'max:40'],
            'username' => ['bail', 'required', 'regex:'.config('regex.username'), 'unique:users,username'],
            'phone' => ['bail', 'required', 'numeric', 'unique:users,phone'],
            'password' => [
                'required',
                RulesPassword::min(8)->
                    mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(3),
            ],
            'alias' => ['bail', 'required', 'not_regex:'.config('regex.not_fully_numbers_symbols')],
        ], [
            'name.required' => $this->translateErrorMessage('name', 'required'),
            'name.not_regex' => $this->translateErrorMessage('name', 'not_fully_numbers_symbols'),
            'name.max' => $this->translateErrorMessage('name', 'max.numeric'),
            'username.required' => $this->translateErrorMessage('username', 'required'),
            'username.regex' => $this->translateErrorMessage('username', 'username.regex'),
            'username.unique' => $this->translateErrorMessage('username', 'exists'),
            'phone.required' => $this->translateErrorMessage('phone', 'required'),
            'password.required' => $this->translateErrorMessage('password', 'required'),
            'phone.numeric' => $this->translateErrorMessage('phone', 'numeric'),
            'phone.unique' => $this->translateErrorMessage('phone', 'exists'),
            'alias.required' => $this->translateErrorMessage('alias', 'required'),
            'alias.not_regex' => $this->translateErrorMessage('alias', 'not_fully_numbers_symbols'),
        ]);
        if ($validator->fails()) {
            return $this->validation_errors($validator->errors());
        }

        // Then everything is valid
        $name = $this->sanitizeString($request->name);
        $username = $this->sanitizeString($request->username);
        $phone = $this->sanitizeString($request->phone);
        $alias = $this->sanitizeString($request->alias);
        $visitor = User::create([
            'full_name' => $name,
            'username' => $username,
            'password' => Hash::make($request->password),
            'phone' => $phone,
            'role_id' => Role::where('name', 'visitor')->value('id'),
        ]);
        $visitor_info = VisitorRecipe::create([
            'visitor_id' => $visitor->id,
            'alias' => $alias,
            'details' => [],
            'random_number' => $this->generateRandomNumberForVisitor(),
        ]);
        $visitor_info->name = $name;
        $visitor_info->username = $username;
        $visitor_info->phone = $phone;

        return $this->resourceResponse(new VisitorAccountResource($visitor_info));
    }

    public function ForgotVisitorRandomNumber(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'handle' => ['required'],
        ], [
            'handle.required' => $this->translateErrorMessage('handle', 'required'),
        ]);
        if ($validator->fails()) {
            return $this->validation_errors($validator->errors());
        }
        $handle = $request->input('handle');
        if (
            $visitor = User::where(function ($query) use ($handle) {
                $query->where('username', $handle)
                    ->orWhere('phone', $handle);
            })->first(['id'])) {
            $data = [];
            $cnt = 0;
            foreach (VisitorRecipe::where('visitor_id', $visitor->id)->get(['random_number', 'alias']) as $recipe) {
                $data[$cnt]['random_number'] = $recipe->random_number;
                $data[$cnt]['alias'] = $recipe->alias;
                ++$cnt;
            }

            return $this->resourceResponse($data);
        }

        return $this->notFoundResponse('There Is No User With That Handle');
    }
}
