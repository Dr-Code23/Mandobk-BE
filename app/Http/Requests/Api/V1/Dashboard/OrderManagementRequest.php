<?php

namespace App\Http\Requests\Api\V1\Dashboard;

use App\Traits\HttpResponse;
use App\Traits\Translatable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class OrderManagementRequest extends FormRequest
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
            'approve' => ['required', 'boolean'],
        ];
    }

    public function messages()
    {
        return [
            'approve.requried' => $this->translateErrorMessage('approve', 'required'),
            'approve.boolean' => $this->translateErrorMessage('approve', 'boolean'),
        ];
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new ValidationException($validator, $this->validation_errors($validator->errors()));
    }
}