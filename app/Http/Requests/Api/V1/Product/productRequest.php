<?php

namespace App\Http\Requests\Api\V1\Product;

use App\Traits\HttpResponse;
use App\Traits\roleTrait;
use App\Traits\translationTrait;
use App\Traits\userTrait;
use Illuminate\Foundation\Http\FormRequest;

class productRequest extends FormRequest
{
    use translationTrait;
    use HttpResponse;
    use userTrait;
    use roleTrait;

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
        // $admin_roles = [
        //     Role::where('name', 'ceo')->first(['id'])->id,
        //     Role::where('name', 'data_entry')->first(['id'])->id,
        // ];
        // $authenticated_user_role_id = $this->getAuthenticatedUserInformation()->role_id;
        $double = ['required', 'numeric', 'min:0.1'];
        $rules = [
            'commercial_name' => ['required', 'max:255'],
            'scientific_name' => ['required', 'max:255'],
            'quantity' => ['required', 'regex:'.config('regex.integer')],
            'concentrate' => $double,
            'bonus' => $double,
            'selling_price' => $double,
            'purchase_price' => $double,
            'patch_number' => ['required'],
            'expire_date' => ['bail', 'required', 'date_format:Y-m-d', 'after:today'],
            'provider' => ['required', 'numeric'],
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

    /**
     * Return Custom Error Messages.
     */
    public function messages(): array
    {
        $messages = [
            'limited.boolean' => $this->translateErrorMessage('limited', 'limited.boolean'),
            'entry_date.date_format' => $this->translateErrorMessage('entry_date', 'entry_date.date.date_format'),
            'expire_date.date_format' => $this->translateErrorMessage('expire_date', 'expire_date.date.date_format'),
            'expire_date.after' => $this->translateErrorMessage('expire_date', 'after'),
            'provider.numeric' => $this->translateErrorMessage('provider', 'numeric'),
            'generate_another_bar_code' => $this->translateErrorMessage('bar_code', 'boolean'),
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
            if (!in_array($key, ['patch_number', 'quantity '])) {
                $messages["$key.numeric"] = $this->translateErrorMessage($key, 'numeric');
                $messages["$key.min"] = $this->translateErrorMessage($key, 'min.numeric');
            } else {
                $messages["$key.regex"] = $this->translateErrorMessage($key, $key.'.regex');
            }
        }

        // Max Length Data
        $max_length_names = ['commercial_name', 'scientific_name'];
        foreach ($max_length_names as $key) {
            $messages["$key.max"] = $this->translateErrorMessage($key, "$key.max");
        }

        return $messages;
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException($validator, $this->validation_errors($validator->errors()));
    }
}
