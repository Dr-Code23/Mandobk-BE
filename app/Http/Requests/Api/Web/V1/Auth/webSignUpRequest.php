<?php

namespace App\Http\Requests\Api\Web\V1\Auth;

use App\Traits\HttpResponse;
use App\Traits\translationTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password as RulesPassword;

class webSignUpRequest extends FormRequest
{
    use HttpResponse;
    use translationTrait;
    private string $file_name = 'Auth/signupTranslationFile.';

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
            'full_name' => ['required'],
            'username' => ['required', 'regex:'.config('regex.username'), 'unique:users,username'],
            'phone' => 'required',
            'role' => ['required'],
            'password' => [
                'required',
                RulesPassword::min(8)->
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
            'full_name.required' => $this->translateErrorMessage($this->file_name.'full_name', 'required'),
            'phone.required' => $this->translateErrorMessage($this->file_name.'phone_number', 'required'),
            'role.required' => $this->translateErrorMessage($this->file_name.'role_name', 'required'),
            'username.required' => $this->translateErrorMessage($this->file_name.'username', 'required'),
            'password.required' => $this->translateErrorMessage($this->file_name.'password', 'required'),
            'full_name.max' => $this->translateErrorMessage($this->file_name.'full_name', 'max.string'),
            'username.regex' => $this->translateErrorMessage($this->file_name.'username', 'regex'),
            'username.unique' => $this->translateErrorMessage($this->file_name.'username', 'unique'),
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException($validator, $this->validation_errors([$validator->errors()]));
    }
}
