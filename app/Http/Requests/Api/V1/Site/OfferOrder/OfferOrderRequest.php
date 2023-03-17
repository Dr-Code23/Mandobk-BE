<?php

namespace App\Http\Requests\Api\V1\Site\OfferOrder;

use App\Traits\HttpResponse;
use App\Traits\Translatable;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class OfferOrderRequest extends FormRequest
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
            'offer_id' => ['required', 'numeric'],
            'quantity' => ['required', 'numeric', 'min:1'],
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'offer_id.required' => $this->translateErrorMessage('offer', 'required'),
            'offer_id.numeric' => $this->translateErrorMessage('offer', 'numeric'),
            'quantity.required' => $this->translateErrorMessage('quantity', 'required'),
            'quantity.numeric' => $this->translateErrorMessage('quantity', 'numeric'),
            'quantity.min' => $this->translateErrorMessage('quantity', 'min.numeric'),
        ];
    }

    /**
     * @param Validator $validator
     * @return void
     * @throws ValidationException
     */
    public function failedValidation(Validator $validator): void
    {
        throw new ValidationException(
            $validator,
            $this->validationErrorsResponse($validator->errors())
        );
    }
}
