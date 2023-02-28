<?php

namespace App\Http\Requests\Api\V1\Users;

use App\Traits\HttpResponse;
use App\Traits\Translatable;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class ForgotVisitorRandomNumberRequest extends FormRequest
{
    use HttpResponse;
    use Translatable;
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
            'handle' => ['required'],
        ];
    }

    public function messages()
    {
        return [
            'handle.required' => $this->translateErrorMessage('handle', 'required'),
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator , $this->validation_errors($validator->errors()));
    }
}
