<?php

namespace App\Http\Requests\Api\V1\Dashboard;

use App\Traits\HttpResponse;
use App\Traits\TranslationTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password as RulesPassword;

class monitorAndEvaluationRequest extends FormRequest
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
        // Check If The Process Is Update
        $except = '';
        if ($this->method() == 'PUT') {
            $except = ','.$this->route('user')->id.',id';
        }

        return [
            'full_name' => ['required'],
            'username' => ['bail', 'required', 'regex:'.config('regex.username'), 'unique:users,username'.$except],
            'role' => ['required'],
            'password' => [
                $this->method() == 'post' ? 'required' : 'sometimes',
                RulesPassword::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(3),
            ],
        ];
    }

    public function messages()
    {
        return [
            'full_name.required' => $this->translateErrorMessage('full_name', 'required'),
            'role.required' => $this->translateErrorMessage('role', 'required'),
            'username.required' => $this->translateErrorMessage('username', 'required'),
            'password.required' => $this->translateErrorMessage('password', 'required'),
            'username.regex' => $this->translateErrorMessage('username', 'username.regex'),
            'username.unique' => $this->translateErrorMessage('username', 'unique'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException($validator, $this->validation_errors([$validator->errors()]));
    }
}
