<?php

namespace App\Http\Requests\Api\Web\V1\Dashboard;

use App\Traits\HttpResponse;
use App\Traits\translationTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class marktingRequest extends FormRequest
{
    use translationTrait;
    use HttpResponse;
    private string $file_name = 'Dashboard/marktingTranslationFile.';

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
            'medicine_name' => ['required', 'not_regex:'.config('regex.not_fully_numbers_symbols')],
            'company_name' => ['required', 'not_regex:'.config('regex.not_fully_numbers_symbols')],
            'discount' => ['bail', 'required', 'numeric', 'between:0,100'],
            'img' => [$this->routeIs('markting_store') ? 'required' : 'sometimes', 'mimes:png,svg,jpg,jpeg'],
        ];
    }

    public function messages()
    {
        return [
            'medicine_name.required' => $this->translateErrorMessage($this->file_name.'medicine_name', 'required'),
            'medicine_name.not_regex' => $this->translateErrorMessage($this->file_name.'medicine_name', 'not_fully_numbers_symbols'),
            'company_name.required' => $this->translateErrorMessage($this->file_name.'company_name', 'required'),
            'company_name.not_regex' => $this->translateErrorMessage($this->file_name.'company_name', 'not_fully_numbers_symbols'),
            'discount.required' => $this->translateErrorMessage($this->file_name.'discount', 'required'),
            'discount.numeric' => $this->translateErrorMessage($this->file_name.'discount', 'numeric'),
            'discount.between' => $this->translateErrorMessage($this->file_name.'discount', 'between.numeric'),
            'img.required' => $this->translateErrorMessage($this->file_name.'img', 'required'),
            'img.mimes' => $this->translateErrorMessage($this->file_name.'img', 'mimes'),
        ];
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new ValidationException($validator, $this->validation_errors($validator->errors()));
    }
}
