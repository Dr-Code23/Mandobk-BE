<?php

namespace App\Http\Requests\Api\V1\Offers;

use App\Traits\HttpResponse;
use App\Traits\Translatable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class OfferRequest extends FormRequest
{
    use Translatable;
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
            'product_id' => ['required'],
            'bonus' => ['required', 'numeric', 'min:1'],
            'start_date' => ['required', 'date', 'date_format:Y-m-d', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'date_format:Y-m-d', 'after:start_date'],
            'pay_method' => ['required'],
        ];
    }

    public function messages()
    {
        $messages = [
            'bonus.min' => $this->translateErrorMessage('bonus', 'min.numeric'),
            'start_date.after_or_equal' => $this->translateErrorMessage('start_date', 'after_or_equal'),
            'end_date.after' => $this->translateErrorMessage('end_date', 'after'),
        ];
        foreach (array_keys($this->rules()) as $key) {
            $messages[$key . '.required'] = $this->translateErrorMessage($key, 'required');
        }

        foreach (['date', 'date_format'] as $rule) {
            foreach (['start_date', 'end_date'] as $key) $messages["$key.$rule"] = $this->translateErrorMessage($key, $rule);
        }
        // Validate Date
        return $messages;
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new ValidationException($validator, $this->validation_errors($validator->errors()));
    }
}
