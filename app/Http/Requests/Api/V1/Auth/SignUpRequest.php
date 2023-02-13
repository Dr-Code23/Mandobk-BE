<?php

namespace App\Http\Requests\Api\V1\Auth;

use App\Traits\HttpResponse;
use App\Traits\TranslationTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password as RulesPassword;

class signUpRequest extends FormRequest
{
    use HttpResponse;
    use TranslationTrait;

    // protected $stopOnFirstFailure = true;

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
        return [
            'full_name' => ['required'],
            'username' => ['required', 'regex:'.config('regex.username'), 'unique:users,username'],
            'phone' => 'required',
            'role' => ['required'],
            'password' => [
                'required',
                RulesPassword::min(8)->
                    mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ];
    }

    public function messages()
    {
        return [
            'full_name.required' => $this->translateErrorMessage('full_name', 'required'),
            'phone.required' => $this->translateErrorMessage('phone', 'required'),
            'role.required' => $this->translateErrorMessage('role', 'required'),
            'username.required' => $this->translateErrorMessage('username', 'required'),
            'password.required' => $this->translateErrorMessage('password', 'required'),
            'full_name.max' => $this->translateErrorMessage('full_name', 'max.string'),
            'username.regex' => $this->translateErrorMessage('username', 'username.regex'),
            'username.unique' => $this->translateErrorMessage('username', 'unique'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException($validator, $this->validation_errors([$validator->errors()]));
    }
}
