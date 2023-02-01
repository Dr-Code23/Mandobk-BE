<?php

namespace App\Http\Requests\Api\Web\V1\Company\CompanyOffers;

use App\Traits\HttpResponse;
use App\Traits\translationTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class companyOfferRequest extends FormRequest
{
    use translationTrait;
    use HttpResponse;
    private string $file_name = 'Company/CompanyOffers/companyOffersTranslationFile.';

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
            'commercial_name' => ['required', 'max:255'],
            'scientefic_name' => ['required', 'max:255'],
            'bonus' => ['required', 'numeric', 'min:0.1'],
            'expire_date' => ['bail', 'required', 'date_format:Y-m-d'],
            'offer_duration' => ['required'],
            'pay_method' => ['required'],
        ];
    }

    public function messages()
    {
        $messages = [
            'bonus.numeric' => $this->translateErrorMessage($this->file_name.'bonus', 'numeric'),
            'bonus.min' => $this->translateErrorMessage($this->file_name.'bonus', 'min.numeric'),
            'expire_date.date_format' => $this->translateErrorMessage($this->file_name.'expire_date', 'expire_date.date.date_format'),
        ];
        foreach (array_keys($this->rules()) as $key) {
            $messages[$key.'.required'] = $this->translateErrorMessage($this->file_name.$key, 'required');
        }

        // Max Length Data
        $max_length_names = ['commercial_name', 'scientefic_name'];
        foreach ($max_length_names as $key) {
            $messages["$key.max"] = $this->translateErrorMessage($this->file_name.$key, "$key.max");
        }

        return $messages;
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new ValidationException($validator, $this->validation_errors($validator->errors()));
    }
}
