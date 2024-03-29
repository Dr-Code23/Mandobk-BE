<?php

namespace App\Http\Requests\Api\V1\Visitor;

use App\Traits\HttpResponse;
use App\Traits\Translatable;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class AddRandomNumberForVisitor extends FormRequest
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
            'username' => ['required'],
            'alias' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => $this->translateErrorMessage('username', 'required'),
            'alias.required' => $this->translateErrorMessage('alias', 'required'),
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
