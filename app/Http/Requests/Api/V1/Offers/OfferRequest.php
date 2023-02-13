<?php

namespace App\Http\Requests\Api\V1\Offers;

use App\Traits\HttpResponse;
use App\Traits\TranslationTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class OfferRequest extends FormRequest
{
    use TranslationTrait;
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
            'product_id' => ['required', 'numeric'],
            'bonus' => ['required', 'numeric', 'min:0.1'],
            'offer_duration' => ['required'],
            'pay_method' => ['required'],
        ];
    }

    public function messages()
    {
        $messages = [
            'product_id.required' => $this->translateErrorMessage('product', 'required'),
            'product_id.numeric' => $this->translateErrorMessage('product', 'numeric'),
            'bonus.numeric' => $this->translateErrorMessage('bonus', 'numeric'),
            'bonus.min' => $this->translateErrorMessage('bonus', 'min.numeric'),
        ];
        foreach (array_keys($this->rules()) as $key) {
            $messages[$key.'.required'] = $this->translateErrorMessage($key, 'required');
        }

        return $messages;
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new ValidationException($validator, $this->validation_errors($validator->errors()));
    }
}
