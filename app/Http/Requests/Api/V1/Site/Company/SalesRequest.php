<?php

namespace App\Http\Requests\Api\V1\Site\Company;

use Illuminate\Foundation\Http\FormRequest;

class SalesRequest extends FormRequest
{
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
        $rules = [
        ];
        if ($this->routeIs('company-sales-add')) {
            $rules['storehouse'] = ['required', 'numeric'];
        }

        return $rules;
    }
}
