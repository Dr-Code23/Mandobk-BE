<?php

namespace App\Http\Requests\Api\V1\Offers;

use App\Traits\HttpResponse;
use App\Traits\Translatable;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class ChangeStatusRequest extends FormRequest
{
    use Translatable, HttpResponse;

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
            'status' => ['required', 'boolean']
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => $this->translateErrorMessage('status', 'required'),
            'status.boolean' => $this->translateErrorMessage('status', 'boolean')
        ];
    }

    public function failedValidation(Validator $validator): void
    {
        throw new ValidationException(
            $validator,
            $this->validation_errors($validator->errors())
        );
    }
}
