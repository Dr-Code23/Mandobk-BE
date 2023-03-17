<?php

namespace App\Http\Requests\Api\V1\Site\Recipes;

use App\Traits\HttpResponse;
use App\Traits\Translatable;
use Illuminate\Contracts\Validation\Validator;
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
            'products' => ['required', 'array'],
            'random_number' => ['required', 'numeric'],
            'products.*.product_id' => ['required', 'numeric'],
            'products.*.quantity' => ['required', 'numeric', 'min:1'],
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'random_number.required' => $this->translateErrorMessage('random_number', 'required'),
            'random_number.numeric' => $this->translateErrorMessage('random_number', 'numeric'),
            'products.required' => $this->translateErrorMessage('products', 'required'),
            'products.*.product_id.required' => $this->translateErrorMessage('product', 'required'),
            'products.*.product_id.numeric' => $this->translateErrorMessage('product', 'numeric'),
            'products.*.quantity.required' => $this->translateErrorMessage('quantity', 'required'),
            'products.*.quantity.numeric' => $this->translateErrorMessage('quantity', 'numeric'),
            'products.*.quantity.min' => $this->translateErrorMessage('quantity', 'min.numeric'),
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
            if (!in_array($errorField, ['products', 'random_number', 'move_products_to_archive_if_exists'])) {
                $errorFieldSeperated = explode('.', $errorField);
                $allErrors['products'][$errorFieldSeperated[1]][$errorFieldSeperated[2]] = $errorContent;
                unset($errors[$errorField]);
            }
        }
        $allErrors = array_merge($allErrors, $errors);

        throw new ValidationException(
            $validator,
            $this->validationErrorsResponse($allErrors)
        );
    }
}
