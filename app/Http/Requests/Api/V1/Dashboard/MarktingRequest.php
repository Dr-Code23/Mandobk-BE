<?php

namespace App\Http\Requests\Api\V1\Dashboard;

use App\Traits\HttpResponse;
use App\Traits\Translatable;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class MarktingRequest extends FormRequest
{
    use Translatable, HttpResponse;

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
    public function rules()
    {
        return [
            'medicine_name' => ['required', 'not_regex:' . config('regex.not_fully_numbers_symbols')],
            'company_name' => ['required', 'not_regex:' . config('regex.not_fully_numbers_symbols')],
            'discount' => ['bail', 'required', 'numeric', 'between:0,100'],
            'img' => [
                $this->routeIs('markting_store') ? 'required' : 'sometimes',
                'mimes:png,svg,jpg,jpeg',
                'max:2048'
            ],
        ];
    }

    /**
     * Custom Messages
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'medicine_name.required' => $this->translateErrorMessage('medicine_name', 'required'),
            'medicine_name.not_regex' => $this->translateErrorMessage('medicine_name', 'not_fully_numbers_symbols'),
            'company_name.required' => $this->translateErrorMessage('company_name', 'required'),
            'company_name.not_regex' => $this->translateErrorMessage('company_name', 'not_fully_numbers_symbols'),
            'discount.required' => $this->translateErrorMessage('discount', 'required'),
            'discount.numeric' => $this->translateErrorMessage('discount', 'numeric'),
            'discount.between' => $this->translateErrorMessage('discount', 'between.numeric'),
            'img.required' => $this->translateErrorMessage('img', 'required'),
            'img.mimes' => $this->translateErrorMessage('img', 'mimes'),
        ];
    }

    /**
     * Custom Response
     *
     * @param Validator $validator
     * @throws ValidationException
     */
    public function failedValidation(Validator $validator): void
    {
        throw new ValidationException($validator, $this->validation_errors($validator->errors()));
    }
}
