<?php

namespace App\Http\Requests\Api\V1\Site\OfferOrder;

use App\Traits\HttpResponse;
use App\Traits\translationTrait;
use Illuminate\Foundation\Http\FormRequest;

class OfferOrderRequest extends FormRequest
{
    use translationTrait;
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
            'offer_id' => ['required', 'numeric'],
            'quantity' => ['required', 'numeric', 'min:1'],
        ];
    }

    public function messages()
    {
        return [
            'offer_id.required' => $this->translateErrorMessage('offer', 'required'),
            'offer_id.numeric' => $this->translateErrorMessage('offer', 'numeric'),
            'quantity.required' => $this->translateErrorMessage('quantity', 'required'),
            'quantity.numeric' => $this->translateErrorMessage('quantity', 'numeric'),
            'quantity.min' => $this->translateErrorMessage('quantity', 'min.numeric'),
        ];
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException($validator, $this->validation_errors($validator->errors()));
    }
}
