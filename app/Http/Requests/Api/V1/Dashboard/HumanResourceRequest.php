<?php

namespace App\Http\Requests\Api\V1\Dashboard;

use App\Traits\HttpResponse;
use App\Traits\Translatable;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class HumanResourceRequest extends FormRequest
{
    use Translatable, HttpResponse;

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
        $attendance = '';

        $departure = '';

        if ((int) $this->status == 0) {
            $attendance = $departure = 'required|date_format:H:i';
            $departure .= '|after:attendance';
        }

        $rules = [
            'user_id' => 'required',
            'status' => ['required', 'in:0,1,2'],
            'date' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
        ];

        if ($attendance) {
            $rules['attendance'] = $attendance;
            $rules['departure'] = $departure;
        }

        return $rules;
    }

    /**
     * Custom Validation Messages
     */
    public function messages(): array
    {
        return [
            'user_id.required' => $this->translateErrorMessage('user_id', 'required'),
            'status.required' => $this->translateErrorMessage('status', 'required'),
            'attendance.date_format' => $this->translateErrorMessage('attendance', 'date_format'),
            'attendance.required' => $this->translateErrorMessage('attendance', 'required'),
            'departure.required' => $this->translateErrorMessage('departure', 'required'),
            'departure.date_format' => $this->translateErrorMessage('departure', 'date_format'),
            'departure.after' => $this->translateErrorMessage('departure', 'human_resource.after'),
            'date.required' => $this->translateErrorMessage('date', 'required'),
            'date.date_format' => $this->translateErrorMessage('date', 'date_format'),
            'date.before_or_equal' => $this->translateErrorMessage('date', 'before_or_equal'),
        ];
    }

    /**
     * Custom Validation Response
     *
     * @throws ValidationException
     */
    public function failedValidation(Validator $validator): void
    {
        throw new ValidationException($validator, $this->validationErrorsResponse($validator->errors()));
    }
}
