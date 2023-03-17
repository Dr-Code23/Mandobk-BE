<?php

namespace App\Http\Requests\Api\V1\Auth;

use App\Traits\HttpResponse;
use App\Traits\Translatable;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password as RulesPassword;
use Illuminate\Validation\ValidationException;

class SignupRequest extends FormRequest
{
    use HttpResponse, Translatable;

    // protected $stopOnFirstFailure = true;

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
            'full_name' => ['required','max:255'],
            'username' => ['required', 'regex:' . config('regex.username')],
            'phone' => ['required' , 'numeric'],
            'role' => ['required'],
            'password' => [
                'required',
                RulesPassword::min(8)->mixedCase()
                    ->numbers()
                    ->symbols(),
                'confirmed'
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
            'phone.required' => $this->translateErrorMessage('phone', 'required'),
            'phone.numeric' => $this->translateErrorMessage('phone' , 'numeric'),
            'role.required' => $this->translateErrorMessage('role', 'required'),
            'username.required' => $this->translateErrorMessage('username', 'required'),
            'password.required' => $this->translateErrorMessage('password', 'required'),
            'password.confirmed' => $this->translateErrorMessage('password' , 'confirmed'),
            'full_name.max' => $this->translateErrorMessage('full_name', 'max.string'),
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
        throw new ValidationException($validator, $this->validationErrorsResponse($validator->errors()));
    }
}
