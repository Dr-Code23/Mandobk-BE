<?php

namespace App\Http\Requests\Api\V1\Users;

use App\Traits\HttpResponse;
use App\Traits\Translatable;
use App\Traits\UserTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class ChangeUserStatusRequest extends FormRequest
{
    use Translatable;
    use HttpResponse;
    use UserTrait;

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
            'status' => [
                'required',
                'in:'
                .$this->isActive().','
                .$this->isFrozen().','
                .$this->isDeleted(),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => $this->translateErrorMessage('status', 'required'),
            'status.in' => $this->translateErrorMessage('status', 'in'),
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
