<?php

namespace App\Http\Requests\Api\Web\V1;

use App\Traits\HttpResponse;
use App\Http\Traits\HttpResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule as ValidationRule;
use Illuminate\Validation\Rules\Password as RulesPassword;

class AuthRequest extends FormRequest
{
    use HttpResponse;
    protected $stopOnFirstFailure = true;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $en_regex = config('regex.en');

        return [
            'full_name' => 'required|alpha|max:50',
            'username' => ['required',"regex:$en_regex", 'unique:users,username', 'min:6', 'max:50'],
            'phone' => 'required',
            'role' => ['required', 'in:1,2,3,5'],
            'password' => [
                'required',
                RulesPassword::min(8)->
                    mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(3)
            ],
        ];
    }

    public function messages()
    {
        return [
            'full_name.required' => 'name-required',
            'full_name.alpha' => 'name-invalid',
            'full_name.max' => 'name-long',
            'username.required' => 'username-required',
            'username.min' => 'username-short',
            'username.regex' => 'username-invalid',
            'username.unique' => 'username-exists',
            'username.max' => 'username-long',
            'phone.required' => 'phone-required',
            'role.required' => 'role-required',
            'role.in' => 'role-invalid',
            'password.required' => 'password-required',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException($validator, $this->validation_errors(array ($validator->errors())));
    }
}
