<?php

namespace App\Http\Requests\Api\V1\Archive;

use App\Traits\HttpResponse;
use App\Traits\Translatable;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class MoveProductToArchiveRequest extends FormRequest
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
            'random_number' => ['required', 'numeric'],
        ];
    }

    public function messages(): array
    {
        return [
            'random_number.required' => $this->translateErrorMessage('random_number', 'required'),
            'random_number.numeric' => $this->translateErrorMessage('random_number', 'numeric'),
        ];
    }

    /**
     * @throws ValidationException
     */
    public function failedValidation(Validator $validator): void
    {
        throw new ValidationException($validator, $this->validationErrorsResponse($validator->errors()));
    }
}
