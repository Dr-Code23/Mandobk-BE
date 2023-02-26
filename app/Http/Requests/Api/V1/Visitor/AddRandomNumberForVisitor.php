<?php

namespace App\Http\Requests\Api\V1\Visitor;

use App\Traits\HttpResponse;
use App\Traits\Translatable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class AddRandomNumberForVisitor extends FormRequest
{
    use Translatable;
    use HttpResponse;
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
            'username' => ['required'],
            'alias' => ['required']
        ];
    }
    public function messages()
    {
        return [
            'username.requried' => $this->translateErrorMessage('username', 'required'),
            'alias.required' => $this->translateErrorMessage('alias', 'required')
        ];
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new ValidationException($validator, $this->validation_errors($validator->errors()));
    }
}
