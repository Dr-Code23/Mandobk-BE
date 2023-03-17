<?php

namespace App\Http\Requests\Api\V1\Site\Pharmacy;

use App\Traits\HttpResponse;
use App\Traits\Translatable;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class SubUserRequest extends FormRequest
{
    use HttpResponse;
    use Translatable;

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
        $exists = 'unique:users,username';
        if ($this->method() == 'PUT') {
            $exists .= ','.$this->route('subUser')->id.',id';
        }

        return [
            'name' => ['required' , 'max:255'],
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

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => $this->translateErrorMessage('name', 'required'),
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
    public function failedValidation(Validator $validator): void
    {
        throw new ValidationException(
            $validator,
            $this->validationErrorsResponse($validator->errors())
        );
    }
}
