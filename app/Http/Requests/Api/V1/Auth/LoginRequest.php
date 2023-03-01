<?php

namespace App\Http\Requests\Api\V1\Auth;

use App\Traits\HttpResponse;
use App\Traits\Translatable;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    use HttpResponse, Translatable;

    /**
     * @var bool
     */
    protected $stopOnFirstFailure = false;

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
        return [
            'username' => ['required' , 'max:255'],
            'password' => ['required' , 'max:255'],
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'username.required' => $this->translateErrorMessage('username', 'required'),
            'password.required' => $this->translateErrorMessage('password', 'required'),
        ];
    }

    /**
     * @param Validator $validator
     * @return void
     * @throws ValidationException
     */
    public function failedValidation(Validator $validator): void
    {
        throw new ValidationException($validator, $this->validation_errors($validator->errors()));
    }
}
