<?php

namespace App\Http\Requests\Api\V1\Site\Pharmacy;

use App\Traits\HttpResponse;
use App\Traits\TranslationTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class SubUserRequest extends FormRequest
{
    use HttpResponse;
    use TranslationTrait;

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
        $exists = 'unique:users,username';
        if ($this->method() == 'PUT') {
            $exists .= ','.$this->route('subuser')->id.',id';
        }

        return [
            'name' => ['required'],
            'username' => [
                'required',
                'regex:'.config('regex.username'),
                // $exists,
                $exists,
                ],
            'password' => [
                $this->method() == 'PUT' ? 'sometimes' : 'required',
                Password::min(8)->
                    mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(3),
            ],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => $this->translateErrorMessage('name', 'required'),
            'username.required' => $this->translateErrorMessage('username', 'required'),
            'password.required' => $this->translateErrorMessage('password', 'required'),
            'username.regex' => $this->translateErrorMessage('username', 'username.regex'),
            'username.unique' => $this->translateErrorMessage('username', 'unique'),
        ];
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException($validator, $this->validation_errors($validator->errors()));
    }
}
