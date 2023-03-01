<?php

namespace App\Http\Requests\Api\V1\Dashboard;

use App\Traits\HttpResponse;
use App\Traits\Translatable;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password as RulesPassword;
use Illuminate\Validation\ValidationException;

class MonitorAndEvaluationRequest extends FormRequest
{
    use HttpResponse, Translatable;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        // Check If The Process Is Update
        $except = '';

        if ($this->method() == 'PUT') {
            $except = ',' . $this->route('user')->id . ',id';
        }

        return [
            'full_name' => ['required'],
            'username' => [
                'bail',
                'required',
                'regex:' . config('regex.username'),
                'unique:users,username' . $except
            ],
            'role' => ['required'],
            'password' => [
                $this->method() == 'post' ? 'required' : 'sometimes',
                RulesPassword::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
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

    /**
     * @param Validator $validator
     * @return void
     * @throws ValidationException
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new ValidationException(
            $validator,
            $this->validation_errors([$validator->errors()
            ])
        );
    }
}
