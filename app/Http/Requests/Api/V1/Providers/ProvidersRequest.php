<?php

namespace App\Http\Requests\Api\V1\Providers;

use App\Traits\HttpResponse;
use App\Traits\translationTrait;
use Illuminate\Foundation\Http\FormRequest;

class ProvidersRequest extends FormRequest
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
        return[
            'name' => ['required']
        ];
    }

    public function messages(){
        return [
            'name.required' => $this->translateErrorMessage('name' , 'required'),
            'name.max' => $this->translateErrorMessage('name' , 'max.string')
        ];
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator){
        throw new \Illuminate\Validation\ValidationException($validator, $this->validation_errors($validator->errors()));
    }
}
;
