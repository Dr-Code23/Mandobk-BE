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
            'handle' => ['required', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'handle.required' => $this->translateErrorMessage('handle', 'required'),
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
