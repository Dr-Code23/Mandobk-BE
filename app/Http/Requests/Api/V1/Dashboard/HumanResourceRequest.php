<?php

namespace App\Http\Requests\Api\V1\Dashboard;

use App\Traits\HttpResponse;
use App\Traits\Translatable;
use Illuminate\Foundation\Http\FormRequest;

class HumanResourceRequest extends FormRequest
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
        $attendance = '';
        $departure = '';
        if ($this->status == '0') {
            $attendance = $departure = 'required|date_format:H:i';
            $departure .= '|after:attendance';
        }
        $rules =  [
            'user_id' => 'required',
            'status' => ['required', 'in:0,1,2'],
            // 'attendance' => ['required_if:status,0', 'date_format:H:i'],
            // 'departure' => ['required_if:status,0', 'date_format:H:i', 'after:attendance'],
            'date' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
        ];
        if ($attendance) {
            $rules['attendance'] = $attendance;
            $rules['departure'] = $departure;
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'user_id.required' => $this->translateErrorMessage('user_id', 'required'),
            'status.required' => $this->translateErrorMessage('status', 'required'),
            'attendance.required_if' => $this->translateErrorMessage('attendance', 'human_resource.requried_if'),
            'attendance.date_format' => $this->translateErrorMessage('attendance', 'date_format'),
            'departure.required_if' => $this->translateErrorMessage('departure', 'human_resource.requried_if'),
            'departure.date_format' => $this->translateErrorMessage('departure', 'date_format'),
            'departure.after' => $this->translateErrorMessage('departure', 'human_resource.after'),
            'date.required' => $this->translateErrorMessage('date', 'required'),
            'date.date_format' => $this->translateErrorMessage('date', 'date_format'),
        ];
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException($validator, $this->validation_errors($validator->errors()));
    }
}
