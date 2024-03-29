<?php

namespace App\Http\Requests\Api\V1\Dashboard;

use App\Traits\HttpResponse;
use App\Traits\Translatable;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class OrderManagementRequest extends FormRequest
{
    use HttpResponse, Translatable;

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
            'approve' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'approve.required' => $this->translateErrorMessage('approve', 'required'),
            'approve.boolean' => $this->translateErrorMessage('approve', 'boolean'),
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
