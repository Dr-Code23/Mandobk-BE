<?php

namespace App\Http\Requests\Api\V1\Offers;

use App\Traits\HttpResponse;
use App\Traits\Translatable;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class OfferRequest extends FormRequest
{
    use Translatable;
    use HttpResponse;

    /**
     * Determine if the user is authorized to make this request.
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
            'product_id' => ['required'],
            'start_date' => [
                'required',
                'date',
                'date_format:Y-m-d',
                'after_or_equal:today',
            ],
            'end_date' => [
                'required',
                'date',
                'date_format:Y-m-d',
                'after:start_date',
            ],
            'pay_method_id' => ['required'],
        ];
    }

    public function messages(): array
    {
        $messages = [
            'start_date.after_or_equal' => $this->translateErrorMessage('start_date', 'after_or_equal'),
            'end_date.after' => $this->translateErrorMessage('end_date', 'after'),
        ];
        foreach (array_keys($this->rules()) as $key) {
            $messages[$key.'.required'] = $this->translateErrorMessage($key, 'required');
        }

        foreach (['date', 'date_format'] as $rule) {
            foreach (['start_date', 'end_date'] as $key) {
                $messages["$key.$rule"] = $this->translateErrorMessage($key, $rule);
            }
        }
        // Validate Date
        return $messages;
    }

    /**
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
