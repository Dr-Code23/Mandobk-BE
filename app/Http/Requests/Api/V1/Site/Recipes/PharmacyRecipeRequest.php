<?php

namespace App\Http\Requests\Api\V1\Site\Recipes;

use App\Rules\HasCommercialName;
use App\Traits\HttpResponse;
use App\Traits\Translatable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class PharmacyRecipeRequest extends FormRequest
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
            'data' => ['required', 'array'],
            'data.*.commercial_name' => ['required'],
            'data.*.quantity' => ['required', 'numeric'],
            'data.*.alternative_commercial_name' => ['sometimes']
        ];
    }

    public function messages()
    {
        return [
            'data.required' => $this->translateErrorMessage('data', 'required'),
            'data.array' => $this->translateErrorMessage('data', 'array'),
            'data.*.commercial_name.required' => $this->translateErrorMessage('commercial_name', 'required'),
            'data.*.quantity.numeric' => $this->translateErrorMessage('quantity', 'numeric'),
        ];
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $errors = $validator->errors()->toArray();
        $allErrors = [];
        foreach ($errors as $errorField => $errorContent) {
            if ($errorField != 'data') {
                $errorFieldSeperated = explode('.', $errorField);
                $allErrors[$errorFieldSeperated[1]][$errorFieldSeperated[2]] = $errorContent;
                unset($errors[$errorField]);
            }
        }
        $allErrors = array_merge($allErrors, $errors);
        throw new ValidationException($validator, $this->validation_errors($allErrors));
    }
}
