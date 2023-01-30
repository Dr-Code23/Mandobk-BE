<?php

namespace App\Http\Requests\Api\Web\V1\Dashboard;

use App\Traits\HttpResponse;
use App\Traits\translationTrait;
use Illuminate\Foundation\Http\FormRequest;

class humanResourceRequest extends FormRequest
{
    use translationTrait;
    use HttpResponse;
    private string $file_name = 'Dashboard/humanResourceTranslationFile.';

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
            'user_id' => 'required',
            'status' => ['required', 'in:0,1,2'],
            'attendance' => ['required_if:status,0', 'date_format:H:i'],
            'departure' => ['required_if:status,0', 'date_format:H:i', 'after:attendance'],
            'date' => ['required', 'date_format:Y-m-d'],
        ];
    }

    public function messages()
    {
        return [
            'user_id.required' => $this->translateErrorMessage($this->file_name.'user_id', 'required'),
            'status.required' => $this->translateErrorMessage($this->file_name.'status', 'required'),
            'attendance.required_if' => $this->translateErrorMessage($this->file_name.'attendance', 'human_resource.requried_if'),
            'attendance.date_format' => $this->translateErrorMessage($this->file_name.'attendance', 'date_format'),
            'departure.required_if' => $this->translateErrorMessage($this->file_name.'departure', 'human_resource.requried_if'),
            'departure.date_format' => $this->translateErrorMessage($this->file_name.'departure', 'date_format'),
            'departure.after' => $this->translateErrorMessage($this->file_name.'departure', 'human_resource.after'),
            'date.required' => $this->translateErrorMessage($this->file_name.'date', 'required'),
            'date.date_format' => $this->translateErrorMessage($this->file_name.'date', 'date_format'),
        ];
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException($validator, $this->validation_errors($validator->errors()));
    }
}
