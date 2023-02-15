<?php

namespace App\Http\Requests\Api\V1\Site\Recipes;

use App\Traits\HttpResponse;
use App\Traits\Translatable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class RecipeRequest extends FormRequest
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
            'products' => ['required', 'array'],
            'random_number' => ['required', 'numeric'],
            'move_products_to_archive_if_exists' => ['sometimes', 'boolean'],
            'products.*.product_id' => ['required', 'numeric'],
            'products.*.quantity' => ['required', 'numeric', 'min:1'],
        ];
    }

    public function messages()
    {
        return [
            'random_number.required' => $this->translateErrorMessage('random_number', 'required'),
            'random_number.numeric' => $this->translateErrorMessage('random_number', 'numeric'),
            'move_products_to_archive_if_exists.boolean' => $this->translateErrorMessage('move_products_to_archive_if_exists', 'boolean'),
            'products.required' => $this->translateErrorMessage('products', 'required'),
            'products.*.product_id.required' => $this->translateErrorMessage('product', 'required'),
            'products.*.product_id.numeric' => $this->translateErrorMessage('product', 'numeric'),
            'products.*.quantity.required' => $this->translateErrorMessage('quantity', 'required'),
            'products.*.quantity.numeric' => $this->translateErrorMessage('quantity', 'numeric'),
            'products.*.quantity.min' => $this->translateErrorMessage('quantity', 'min.numeric'),
        ];
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $errors = $validator->errors()->toArray();
        $allErrors = [];
        foreach ($errors as $errorField => $errorContent) {
            if (!in_array($errorField, ['products', 'random_number', 'move_products_to_archive_if_exists'])) {
                $errorFieldSeperated = explode('.', $errorField);
                $allErrors['products'][$errorFieldSeperated[1]][$errorFieldSeperated[2]] = $errorContent;
                unset($errors[$errorField]);
            }
        }
        $allErrors = array_merge($allErrors, $errors);
        throw new ValidationException($validator, $this->validation_errors($allErrors));
    }
}
