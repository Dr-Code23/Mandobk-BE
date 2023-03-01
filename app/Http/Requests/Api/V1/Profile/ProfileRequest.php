<?php

namespace App\Http\Requests\Api\V1\Profile;

use App\Traits\HttpResponse;
use App\Traits\Translatable;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password as RulesPassword;
use Illuminate\Validation\ValidationException as ValidationValidationException;

class ProfileRequest extends FormRequest
{
    use Translatable;
    use HttpResponse;

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
            'full_name' => ['required' , 'max:255'],
            'password' => [
                'sometimes',
                RulesPassword::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
            'avatar' => [
                'sometimes',
                'image',
                'mimes:png,jpg',
                'max:2048'
            ],
            'phone' => [
                'required',
                'unique:users,phone,' . auth()->id() . ',id',
                'numeric'
            ]
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'full_name.required' => $this->translateErrorMessage('full_name', 'required'),
            'avatar.required' => $this->translateErrorMessage('avatar', 'required'),
            'avatar.image' => $this->translateErrorMessage('avatar', 'image'),
            'avatar.mimes' => $this->translateErrorMessage('avatar', 'mimes'),
            'avatar.max' => $this->translateErrorMessage('avatar', 'max.file'),
            'phone.required' => $this->translateErrorMessage('phone', 'required'),
            'phone.unique' => $this->translateErrorMessage('phone', 'unique')
        ];
    }

    /**
     * @param Validator $validator
     * @return void
     * @throws ValidationValidationException
     */
    public function failedValidation(Validator $validator): void
    {
        throw new ValidationValidationException(
            $validator,
            $this->validation_errors($validator->errors())
        );
    }
}
