<?php

namespace App\Http\Requests\Api\V1\Product;

use App\Traits\HttpResponse;
use App\Traits\RoleTrait;
use App\Traits\Translatable;
use App\Traits\UserTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class ProductRequest extends FormRequest
{
    use Translatable;
    use HttpResponse;
    use UserTrait;
    use RoleTrait;

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
     * Return Custom Error Messages.
     */
    public function messages(): array
    {
        $messages = [
            'limited.boolean' => $this->translateErrorMessage('limited', 'limited.boolean'),
            'entry_date.date_format' =>
                $this->translateErrorMessage('entry_date', 'entry_date.date.date_format'),
            'expire_date.date_format' =>
                $this->translateErrorMessage('expire_date', 'expire_date.date.date_format'),
            'expire_date.after' => $this->translateErrorMessage('expire_date', 'after'),
            'generate_another_bar_code' => $this->translateErrorMessage('bar_code', 'boolean'),
            'quantity.regex' => $this->translateErrorMessage('quantity', 'quantity.regex'),
        ];

        // get all fields names
        $required_keys = array_keys($this->rules());

        // All Values Are Required
        foreach ($required_keys as $key) {
            $messages["$key.required"] = $this->translateErrorMessage($key, 'required');
        }

        // Numeric , between and regex validation messages
        $regex_length_names = ['quantity', 'purchase_price', 'selling_price', 'bonus', 'patch_number', 'concentrate'];
        foreach ($regex_length_names as $key) {
            if ($key != 'patch_number') {
                $messages["$key.numeric"] = $this->translateErrorMessage($key, 'numeric');
                $messages["$key.min"] = $this->translateErrorMessage($key, 'min.numeric');
            } else {
                $messages["$key.regex"] = $this->translateErrorMessage($key, $key . '.regex');
            }
        }

        // Max Length Data
        $max_length_names = ['commercial_name', 'scientific_name'];
        foreach ($max_length_names as $key) {
            $messages["$key.max"] = $this->translateErrorMessage($key, "$key.max");
        }

        return $messages;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $double = ['required', 'numeric', 'min:0.1'];
        $rules = [
            'commercial_name' => ['required'],
            'scientific_name' => ['required'],
            'quantity' => ['required', 'regex:' . config('regex.integer')],
            'concentrate' => $double,
            'bonus' => $double,
            'selling_price' => $double,
            'purchase_price' => $double,
            'patch_number' => ['required'],
            'expire_date' => ['bail', 'required', 'date_format:Y-m-d', 'after:today'],
        ];

        if ($this->method() == 'POST') {
            $rules['barcode'] = ['required', 'numeric'];
        }

        if ($this->roleNameIn(['ceo', 'data_entry'])) {
            $rules['limited'] = ['required', 'boolean'];
        }

        if ($this->method() == 'PUT') {
            $rules['barcode'] = ['sometimes', 'numeric'];
            // $rules['generate_another_bar_code'] = ['sometimes', 'boolean'];
        }

        return $rules;
    }

    public function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, $this->validation_errors($validator->errors()));
    }
}
