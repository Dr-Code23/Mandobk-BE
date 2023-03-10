<?php

namespace App\Http\Requests\Api\V1\Site\Recipes;

use App\Traits\HttpResponse;
use App\Traits\Translatable;
use Illuminate\Contracts\Validation\Validator;
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
            'data' => ['required', 'array'],
            'data.*.commercial_name' => ['required' , 'max:255'],
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'data.required' =>
                $this->translateErrorMessage('data', 'required'),
            'data.array' =>
                $this->translateErrorMessage('data', 'array'),
            'data.*.commercial_name.required' =>
                $this->translateErrorMessage('commercial_name', 'required')
        ];
    }

    /**
     * @param Validator $validator
     * @return void
     * @throws ValidationException
     */
    public function failedValidation(Validator $validator): void
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

        throw new ValidationException(
            $validator,
            $this->validation_errors($allErrors)
        );
    }
}
