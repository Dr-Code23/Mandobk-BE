<?php

namespace App\Http\Requests\Api\V1\Users;

use App\Traits\HttpResponse;
use App\Traits\Translatable;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class RegisterVisitorRequest extends FormRequest
{
    use Translatable;
    use HttpResponse;

    /**
     * Determine if the user is authorized to make this request.
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
        return [
            'name' => [
                'bail',
                'required',
                'not_regex:'.config('regex.not_fully_numbers_symbols'),
                'max:255',
            ],
            'username' => [
                'bail',
                'required',
                'regex:'.config('regex.username'),
                'unique:users,username',
            ],
            'phone' => [
                'bail',
                'required',
                'numeric',
                'unique:users,phone',
            ],
            'password' => [
                'required',
                Password::min(8)->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(3),
            ],
            'alias' => [
                'bail',
                'required',
                'not_regex:'.config('regex.not_fully_numbers_symbols'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
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
        ];
    }

    /**
     * @throws ValidationException
     */
    public function failedValidation(Validator $validator): void
    {
        throw new ValidationException(
            $validator,
            $this->validationErrorsResponse($validator->errors())
        );
    }
}
