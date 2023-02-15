<?php

namespace App\Http\Requests\Api\V1\Sales;

use App\Traits\HttpResponse;
use App\Traits\Translatable;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class SaleRequest extends FormRequest
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
            'data' => ['required', 'array'],
            'data.*.product_id' => ['required'],
            'data.*.expire_date' => ['sometimes'],
            'data.*.selling_price' => ['required', 'numeric', 'min:1'],
            'data.*.quantity' => ['required', 'numeric', 'min:1'],
            'buyer_id' => ['required'],
        ];
    }

    public function messages()
    {
        return [
            'data.required' => $this->translateErrorMessage('data', 'required'),
            'buyer_id.required' => $this->translateErrorMessage('the_buyer', 'required'),
            'data.*.product_id.required' => $this->translateErrorMessage('product', 'required'),
            'data.*.expire_date.required' => $this->translateErrorMessage('expire_date', 'required'),
            'data.*.expire_date.date_format' => $this->translateErrorMessage('expire_date', 'date_format'),
            'data.*.selling_price.required' => $this->translateErrorMessage('selling_price', 'required'),
            'data.*.selling_price.numeric' => $this->translateErrorMessage('selling_price', 'numeric'),
            'data.*.selling_price.min' => $this->translateErrorMessage('selling_price', 'min.numeric'),
            'data.*.quantity.required' => $this->translateErrorMessage('quantity', 'required'),
            'data.*.quantity.numeric' => $this->translateErrorMessage('quantity', 'numeric'),
            'data.*.quantity.min' => $this->translateErrorMessage('quantity', 'min.numeric'),
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->toArray();
        $allErrors = [];
        foreach ($errors as $errorField => $errorContent) {
            if (!in_array($errorField, ['data', 'buyer_id'])) {
                $errorFieldSeperated = explode('.', $errorField);
                $allErrors['data'][$errorFieldSeperated[1]][$errorFieldSeperated[2]] = $errorContent;
                unset($errors[$errorField]);
            }
        }
        $allErrors = array_merge($allErrors, $errors);
        throw new ValidationException($validator, $this->validation_errors($allErrors));
    }
}
