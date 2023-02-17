<?php

namespace App\Http\Requests\Api\V1\Users;

use App\Traits\HttpResponse;
use App\Traits\Translatable;
use App\Traits\UserTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class ChangeUserStatusRequest extends FormRequest
{
    use Translatable;
    use HttpResponse;
    use UserTrait;
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
            'status' => ['required', 'in:' . $this->isActive() . ',' . $this->isFrozen() . ',' . $this->isDeleted()]
        ];
    }

    public function messages()
    {
        return [
            'status.required' => $this->translateErrorMessage('status', 'required'),
            'status.in' => $this->translateErrorMessage('status', 'in')
        ];
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new ValidationException($validator, $this->validation_errors($validator->errors()));
    }
}
