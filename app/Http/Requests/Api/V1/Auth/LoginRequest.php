<?php

namespace App\Http\Requests\Api\V1\Auth;

use App\Traits\HttpResponse;
use App\Traits\translationTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    use HttpResponse;
    use translationTrait;
    protected $stopOnFirstFailure = false;

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
            'username' => ['required'],
            'password' => ['required'],
        ];
    }

    public function messages()
    {
        return [
            'username.required' => $this->translateErrorMessage('username', 'required'),
            'password.required' => $this->translateErrorMessage('password', 'required'),
            // 'password.required' => 'password-required',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException($validator, $this->validation_errors($validator->errors()));
    }
}
